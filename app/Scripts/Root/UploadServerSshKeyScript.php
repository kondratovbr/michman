<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Server;
use App\Models\ServerSshKey;
use App\Scripts\AbstractServerScript;
use phpseclib3\Net\SFTP;

class UploadServerSshKeyScript extends AbstractServerScript
{
    public function execute(Server $server, ServerSshKey $key, string $username, SFTP $ssh = null): void
    {
        $this->init($server, $ssh ?? $server->sftp('root'));

        $remoteDirectory = $username === 'root'
            ? '/root/.ssh'
            : "/home/{$username}/.ssh";
        $remotePrivateKeyFile = $remoteDirectory . '/id_ed25519';
        $remotePublicKeyFile = $remoteDirectory . '/id_ed25519.pub';

        // Create .ssh directory for the user if it doesn't exist.
        $this->exec("mkdir -p -m 0700 {$remoteDirectory}");

        // Force remove identity files in case they exist.
        $this->exec("rm -f {$remotePrivateKeyFile}");
        $this->exec("rm -f {$remotePublicKeyFile}");

        // Put a private and public part of the SSH key to corresponding identity files.
        $this->exec("echo \"{$key->privateKeyString}\" >> {$remotePrivateKeyFile}");
        $this->exec("echo \"{$key->publicKeyString}\" >> {$remotePublicKeyFile}");

        // // Set proper permissions on the files and directory.
        $this->exec("chmod 0700 {$remotePrivateKeyFile}");
        $this->exec("chmod 0755 {$remotePublicKeyFile}");
        $this->exec("chown -R {$username} {$remoteDirectory}");
        $this->exec("chgrp -R {$username} {$remoteDirectory}");
    }
}
