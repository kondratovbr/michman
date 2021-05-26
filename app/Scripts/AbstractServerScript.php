<?php declare(strict_types=1);

namespace App\Scripts;

use App\Models\Server;
use App\Support\Str;
use phpseclib3\Net\SFTP;
use phpseclib3\Net\SSH2;

abstract class AbstractServerScript
{
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
    protected function setSsh(SFTP $ssh): void
    {
        $this->ssh = $ssh;
    }

    /**
     * Initialize an SSH session.
     */
    private function initialize(): SFTP
    {
        if (! isset($this->server))
            throw new \RuntimeException('Server model instance is not set. It is required to perform any actions over SSH.');

        $this->ssh ??= $this->ssh() ?? $this->server->sftp();

        return $this->ssh;
    }

    /**
     * Execute a command on a remote server over SSH.
     */
    protected function exec(string $command, bool $scrubLogs = false): string|bool
    {
        $this->initialize();

        if ($this->ssh->isPTYEnabled())
            $this->disablePty();

        try {
            return $output = $this->ssh->exec($command);
        } finally {
            $exitCode = $this->ssh->getExitStatus();
            if ($exitCode === false)
                $exitCode = null;

            $this->server->log(
                type: 'exec',
                command: $scrubLogs ? null : $command,
                exitCode: $exitCode,
                content: $scrubLogs ? '[Log is scrubbed for security reasons.]' : ($output ?? null),
            );
        }
    }

    /**
     * Execute a command on a remote server over SSH using a PTY mode.
     */
    protected function execPty(string $command): bool
    {
        $this->initialize();

        if (! $this->ssh->isPTYEnabled())
            $this->enablePty();

        try {
            return $this->ssh->exec($command);
        } finally {
            $this->server->log(
                type: 'exec_pty',
                command: $command,
            );
        }
    }

    protected function read(string $expected = '', $mode = SSH2::READ_SIMPLE, bool $scrubLogs = false): string
    {
        $this->initialize();

        if (! $this->ssh->isPTYEnabled())
            $this->enablePty();

        try {
            return $output = $this->ssh->read($expected, $mode);
        } finally {
            $this->server->log(
                type: 'read',
                content: $scrubLogs ? '[Log is scrubbed for security reasons.]' : ($output ?? null),
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
    protected function execSudo(string $command, bool $scrubLogs = false): string|bool|null
    {
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
            $exitCode = $this->ssh->getExitStatus();
            if ($exitCode === false)
                $exitCode = null;

            $this->server->log(
                type: 'exec_sudo',
                command: $scrubLogs ? null : $command,
                exitCode: $exitCode,
                content: $scrubLogs ? '[Log is scrubbed for security reasons.]' : ($output ?? null),
            );
        }
    }
}
