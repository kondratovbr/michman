<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Interfaces\SshKeyInterface;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class UploadSshKeyToServerScript extends AbstractServerScript
{
    public function execute(
        Server $server,
        SshKeyInterface $key,
        string $username,
        string $keyName = 'id_ed25519',
        SFTP $ssh = null,
    ): void {
        $this->init($server, $ssh ?? $server->sftp('root'));

        $remoteDirectory = $username === 'root'
            ? '/root/.ssh'
            : "/home/{$username}/.ssh";
        $remotePrivateKeyFile = $remoteDirectory . "/{$keyName}";
        $remotePublicKeyFile = $remoteDirectory . "/{$keyName}.pub";

        // Create .ssh directory for the user if it doesn't exist.
        $this->exec("mkdir -p -m 0700 {$remoteDirectory}");

        // Force remove identity files in case they exist.
        $this->exec("rm -f {$remotePrivateKeyFile}");
        $this->exec("rm -f {$remotePublicKeyFile}");

        // Put a private and public part of the SSH key to corresponding identity files.
        $this->exec("echo \"{$key->privateKeyString}\" >> {$remotePrivateKeyFile}", scrubCommand: true);
        $this->exec("echo \"{$key->publicKeyString}\" >> {$remotePublicKeyFile}", scrubCommand: true);

        // // Set proper permissions on the files and directory.
        $this->exec("chmod 0700 {$remotePrivateKeyFile}");
        $this->exec("chmod 0755 {$remotePublicKeyFile}");
        $this->exec("chown -R {$username} {$remoteDirectory}");
        $this->exec("chgrp -R {$username} {$remoteDirectory}");
    }
}
