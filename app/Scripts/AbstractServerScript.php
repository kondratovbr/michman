<?php declare(strict_types=1);

namespace App\Scripts;

use App\Models\Server;
use App\Scripts\Traits\RunsStatically;
use App\Support\Str;
use Illuminate\Support\Facades\Log;
use phpseclib3\Net\SFTP;
use phpseclib3\Net\SSH2;

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

    /**
     * Set a server attribute.
     */
    protected function setServer($server): void
    {
        $this->server = $server;
    }

    /**
     * Set a current SSH session attribute;
     */
    protected function setSsh(SFTP|null $ssh): void
    {
        $this->ssh = $ssh ?? $this->server->sftp();
    }

    /**
     * Initialize an SSH session.
     */
    protected function initialize(): SFTP
    {
        if (! isset($this->server))
            throw new \RuntimeException('Server model instance is not set. It is required to perform any actions over SSH.');

        $this->ssh ??= $this->ssh() ?? $this->server->sftp();

        return $this->ssh;
    }

    /**
     * Execute a command on a remote server over SSH.
     */
    protected function exec(
        string $command,
        bool $scrubCommand = false,
        bool $scrubOutput = false,
        string $logCommand = null,
    ): string|bool {
        $this->initialize();

        if ($this->ssh->isPTYEnabled())
            $this->disablePty();

        try {
            return $output = $this->ssh->exec($command);
        } finally {
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

    /**
     * Execute a command on a remote server over SSH using a PTY mode.
     */
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

    /**
     * Read an output printed to the PTY.
     */
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

    /**
     * Enable PTY on the current SSH session.
     */
    protected function enablePty(): void
    {
        $this->initialize();

        $this->ssh->enablePTY();
    }

    /**
     * Disable PTY on the current SSH session.
     */
    protected function disablePty(): void
    {
        $this->initialize();

        $this->ssh->disablePTY();
    }

    /**
     * Set a command timeout for the current SSH session.
     */
    protected function setTimeout(int $seconds = 0): void
    {
        $this->initialize();

        $this->ssh->setTimeout($seconds);
    }

    /**
     * Send a local file to the server over SFTP.
     */
    protected function sendFile(string $remotePath, string $localPath): void
    {
        $this->initialize();

        try {
            $success = $this->ssh->put(
                $remotePath,
                $localPath,
                SFTP::SOURCE_LOCAL_FILE,
            );
        } finally {
            $this->server->log(
                type: 'send_file',
                localFile: $localPath,
                remoteFile: $remotePath,
                success: $success ?? false,
            );
        }
    }

    /**
     * Get the exit status of the last executed command.
     */
    protected function getExitStatus(): false|int
    {
        return $this->ssh->getExitStatus();
    }

    /**
     * Execute a command that uses sudo,
     * making sure that the password is supplied if requested.
     */
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

    /**
     * Initialize the script instance - set Server and SSH session.
     */
    protected function init(Server $server, SFTP|null $ssh = null, string $user = 'root'): void
    {
        if (! is_null($ssh) && $user !== 'root')
            Log::warning(static::class . ': init() method received both an SSH instance and a username different from "root". The existing SSH instance will be used regardless of the username provided.');

        $this->setServer($server);
        $this->setSsh($ssh ?? $server->sftp($user));
    }
}
