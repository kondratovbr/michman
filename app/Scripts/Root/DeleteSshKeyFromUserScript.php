<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Interfaces\SshKeyInterface;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Support\SshKeyFormatter;
use phpseclib3\Crypt\Common\PublicKey;
use phpseclib3\Net\SFTP;

// TODO: CRITICAL! Test this.

class DeleteSshKeyFromUserScript extends AbstractServerScript
{
    public function execute(
        Server $server,
        string $username,
        SshKeyInterface|PublicKey|string $sshKey,
        SFTP $rootSsh = null,
    ): void {
        $this->init($server, $rootSsh);

        if (is_string($sshKey))
            $sshKeyString = $sshKey;

        $sshKeyString ??= $sshKey instanceof SshKeyInterface
            ? $sshKey->publicKeyString
            : SshKeyFormatter::format($sshKey, $server->name);

        $remoteDirectory = $username === 'root'
            ? '/root/.ssh'
            : "/home/{$username}/.ssh";
        $remoteFile = $remoteDirectory . '/authorized_keys';

        $this->exec("test -w {$remoteFile}");

        if ($this->failed())
            throw new ServerScriptException("File authorized_keys of user {$username} either not writable at all or doesn't exist.");

        $this->exec("sed -i '/{$sshKeyString}/d' {$remoteFile}");

        if ($this->failed())
            throw new ServerScriptException('Failed to remove the key from authorized_keys file.');
    }
}
