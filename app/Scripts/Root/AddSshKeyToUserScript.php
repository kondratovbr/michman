<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Interfaces\SshKeyInterface;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Support\SshKeyFormatter;
use phpseclib3\Net\SFTP;
use phpseclib3\Crypt\Common\PublicKey;

class AddSshKeyToUserScript extends AbstractServerScript
{
    public function execute(
        Server $server,
        string $username,
        SshKeyInterface|PublicKey|string $sshKey,
        SFTP $rootSsh = null,
    ) {
        $this->init($server, $rootSsh ?? $server->sftp('root'));

        if (is_string($sshKey))
            $sshKeyString = $sshKey;

        $sshKeyString ??= $sshKey instanceof SshKeyInterface
            ? $sshKey->publicKeyString
            : SshKeyFormatter::format($sshKey, $server->name);

        $remoteDirectory = $username === 'root'
            ? '/root/.ssh'
            : "/home/$username/.ssh";
        $remoteFile = $remoteDirectory . '/authorized_keys';

        // Create .ssh directory for the user if it doesn't exist.
        $this->exec("mkdir -p -m 0700 $remoteDirectory");

        // Append a public SSH key to authorized_keys file.
        $this->exec("echo \"$sshKeyString\" >> $remoteFile");

        // Set proper permissions on the file and directory.
        $this->exec("chmod 0600 $remoteFile");
        $this->exec("chown -R $username:$username $remoteDirectory");
    }
}
