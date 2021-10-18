<?php declare(strict_types=1);

namespace App\Scripts;

use App\Models\Server;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Traits\RunsStatically;
use App\Support\Str;
use Illuminate\Support\Facades\Log;
use phpseclib3\Net\SFTP;
use phpseclib3\Net\SSH2;
use RuntimeException;

// TODO: CRITICAL! Cover with tests. Definitely.

abstract class AbstractServerScript
{
    use RunsStatically;

    /** Server to run command on. */
    private Server $server;
    /** SSH session to a remote server with SFTP support. */
    private SFTP $ssh;

    /**
     * Create a new SSH session to a remote server with SFTP support.
     *
     * Override this method to create an SSH session using a method.
     */
    protected function ssh(): SFTP|null
    {
        return null;
    }

    /** Set a server attribute. */
    protected function setServer($server): void
    {
        $this->server = $server;
    }

    /** Set a current SSH session attribute; */
    protected function setSsh(SFTP|null $ssh): void
    {
        $this->ssh = $ssh ?? $this->server->sftp();
    }

    /** Initialize an SSH session. */
    protected function initialize(): SFTP
    {
        if (! isset($this->server))
            throw new RuntimeException('Server model instance is not set. It is required to perform any actions over SSH.');

        $this->ssh ??= $this->ssh() ?? $this->server->sftp();

        return $this->ssh;
    }

    /** Execute a command on a remote server over SSH. */
    protected function exec(
        string $command,
        bool $scrubCommand = false,
        bool $scrubOutput = false,
        string $logCommand = null,
        bool $throw = true,
    ): string|bool {
        $this->initialize();

        if ($this->ssh->isPTYEnabled())
            $this->disablePty();

        try {
            $output = $this->ssh->exec($command);

            if ($throw && $this->failed())
                throw new ServerScriptException('Shell command has failed.');

            return $output;
        } finally {
            $output ??= null;
            $outputToLog = $output === false ? null : $output;
            $exitCode = $this->ssh->getExitStatus();
            if ($exitCode === false)
                $exitCode = null;

            $this->server->log(
                type: 'exec',
                command: $scrubCommand
                    ? ($logCommand ?? '[Command is scrubbed for security reasons.]')
                    : $command,
                exitCode: $exitCode,
                content: $scrubOutput
                    ? '[Output is scrubbed for security reasons.]'
                    : ($outputToLog ?? null),
            );
        }
    }

    /** Execute a command on a remote server over SSH using a PTY mode. */
    protected function execPty(
        string $command,
        bool $scrubCommand = false,
        string $logCommand = null,
    ): bool {
        $this->initialize();

        if (! $this->ssh->isPTYEnabled())
            $this->enablePty();

        try {
            return $this->ssh->exec($command);
        } finally {
            $this->server->log(
                type: 'exec_pty',
                command: $scrubCommand
                    ? ($logCommand ?? '[Command is scrubbed for security reasons.]')
                    : $command,
            );
        }
    }

    /** Read an output printed to the PTY. */
    protected function read(
        string $expected = '',
        $mode = SSH2::READ_SIMPLE,
        bool $scrubOutput = false
    ): string {
        $this->initialize();

        if (! $this->ssh->isPTYEnabled())
            $this->enablePty();

        try {
            return $output = $this->ssh->read($expected, $mode);
        } finally {
            $this->server->log(
                type: 'read',
                content: $scrubOutput
                    ? '[Output is scrubbed for security reasons.]'
                    : ($output ?? null),
            );
        }
    }

    /** Write a string to the shell. */
    protected function write(
        string $input,
        bool $scrubInput = false,
        string $logInput = null,
    ): bool {
        $this->initialize();

        try {
            return $result = $this->ssh->write($input);
        } finally {
            $this->server->log(
                type: 'write',
                content: $scrubInput
                    ? ($logInput ?? '[Input is scrubbed for security reasons.]')
                    : $input,
                success: $result ?? false,
            );
        }
    }

    /** Enable PTY on the current SSH session. */
    protected function enablePty(): void
    {
        $this->initialize();

        $this->ssh->enablePTY();
    }

    /** Disable PTY on the current SSH session. */
    protected function disablePty(): void
    {
        $this->initialize();

        $this->ssh->disablePTY();
    }

    /** Set a command timeout for the current SSH session. */
    protected function setTimeout(int $seconds = 0): void
    {
        $this->initialize();

        $this->ssh->setTimeout($seconds);
    }

    /** Send a local file to the server over SFTP. */
    protected function sendFile(string $remotePath, string $localPath): bool
    {
        $this->initialize();

        try {
            return (bool) $success = $this->ssh->put(
                $remotePath,
                $localPath,
                SFTP::SOURCE_LOCAL_FILE,
            );
        } finally {
            $this->server->log(
                type: 'send_file',
                localFile: $localPath,
                remoteFile: $remotePath,
                success: (bool) ($success ?? false),
            );
        }
    }

    /** Send a string to a remote file on the server over SFTP. */
    protected function sendString(string $remotePath, string $content): bool
    {
        $this->initialize();

        try {
            return (bool) $success = $this->ssh->put(
                $remotePath,
                $content,
                SFTP::SOURCE_STRING,
            );
        } finally {
            $this->server->log(
                type: 'send_string',
                remoteFile: $remotePath,
                success: (bool) ($success ?? false),
            );
        }
    }

    /** Append a string to the end of a remote file on the server over SFTP. */
    protected function appendString(string $remotePath, string $content): bool
    {
        $this->initialize();

        try {
            return (bool) $success = $this->ssh->put(
                $remotePath,
                $content,
                SFTP::RESUME,
            );
        } finally {
            $this->server->log(
                type: 'append_string',
                remoteFile: $remotePath,
                success: (bool) ($success ?? false),
            );
        }
    }

    /** Download a file from a server and return its content as a string. */
    protected function getString(string $remotePath): string|null
    {
        try {
            $result = $this->ssh->get($remotePath);
            return $result === false ? null : $result;
        } finally {
            $this->server->log(
                type: 'get_string',
                remoteFile: $remotePath,
                success: $result !== false,
            );
        }
    }

    /** Get the exit status of the last executed command. */
    protected function getExitStatus(): false|int
    {
        return $this->ssh->getExitStatus();
    }

    /** Check if the previous command has failed. */
    protected function failed(): bool
    {
        return $this->getExitStatus() !== 0;
    }

    /** Execute a command that uses sudo, making sure that the password is supplied if requested. */
    protected function execSudo(
        string $command,
        bool $scrubCommandLog = false,
        bool $scrubOutputLog = false,
        string $logCommand = null,
    ): string|bool|null {
        $this->initialize();

        if (! $this->ssh->isPTYEnabled())
            $this->enablePty();

        try {

            $this->ssh->write($command . "\n");
            $output = $this->ssh->read();

            if (! Str::contains((string) $output ?? '', '[sudo] password for'))
                return $output;

            $this->ssh->write($this->server->sudoPassword . "\n");

            return $output = $this->ssh->read();

        } finally {
            $outputToLog = is_bool($output) ? null : $output;
            $exitCode = $this->ssh->getExitStatus();
            if ($exitCode === false)
                $exitCode = null;

            $this->server->log(
                type: 'exec_sudo',
                command: $scrubCommandLog
                    ? ($logCommand ?? '[Command is scrubbed for security reasons.]')
                    : $command,
                exitCode: $exitCode,
                content: $scrubOutputLog
                    ? '[Output log is scrubbed for security reasons.]'
                    : ($outputToLog ?? null),
            );
        }
    }

    /** Initialize the script instance - set Server and SSH session. */
    protected function init(Server $server, SFTP|null $ssh = null, string $user = 'root'): void
    {
        if (! is_null($ssh) && $user !== 'root')
            Log::warning(static::class . ': init() method received both an SSH instance and a username different from "root". The existing SSH instance will be used regardless of the username provided.');

        $this->setServer($server);
        $this->setSsh($ssh ?? $server->sftp($user));
    }
}
