<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Certificate;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Traits\InteractsWithCertbot;
use phpseclib3\Net\SFTP;

class InstallLetsEncryptCertificateScript extends AbstractServerScript
{
    use InteractsWithCertbot;

    public function execute(Server $server, Certificate $cert, SFTP $rootSsh = null): void
    {
        $this->init($server, $rootSsh);

        $this->certbotReceiveCertificate($cert->domain, $cert->user->email);
    }
}
