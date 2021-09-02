<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Certificate;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use phpseclib3\Net\SFTP;

// TODO: CRITICAL! Test this.

class DeleteLetsEncryptCertificateScript extends AbstractServerScript
{
    public function execute(Server $server, Certificate $cert, SFTP $rootSsh = null): void
    {
        $this->init($server, $rootSsh);

        // TODO: CRITICAL! This may fail for numerous reasons. Should figure out how to inform the user.
        $this->exec("certbot -n delete --cert-name {$cert->name}");

        if ($this->failed())
            throw new ServerScriptException("Certbot command to delete certificate \"{$cert->name}\" has failed.");
    }
}
