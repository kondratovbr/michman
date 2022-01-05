<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Certificate;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Scripts\Traits\InteractsWithCertbot;
use phpseclib3\Net\SFTP;

// TODO: CRITICAL! CONTINUE. Test this.

class DeleteLetsEncryptCertificateScript extends AbstractServerScript
{
    use InteractsWithCertbot;

    public function execute(Server $server, Certificate $cert, SFTP $rootSsh = null): void
    {
        $this->init($server, $rootSsh);

        if (! $this->certbotHasCertificate($cert->name))
            return;

        $this->certbotDeleteCertificate($cert->name);

        if ($this->failed())
            throw new ServerScriptException("Certbot command to delete certificate \"{$cert->name}\" has failed.");
    }
}
