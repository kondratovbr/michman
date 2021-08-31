<?php declare(strict_types=1);

namespace App\Scripts\Root;

use App\Models\Certificate;
use App\Models\Server;
use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use phpseclib3\Net\SFTP;

class InstallLetsEncryptCertificateScript extends AbstractServerScript
{
    public function execute(Server $server, Certificate $certificate, SFTP $rootSsh = null): void
    {
        $this->init($server, $rootSsh);

        $domains = implode(',', $certificate->domains);

        $this->enablePty();
        $this->setTimeout(60 * 30); // 30 min

        // TODO: CRITICAL! This may fail for numerous reasons. Should figure out how to inform the user.
        $this->execPty("certbot certonly -n -m {$certificate->user->email} --agree-tos -d {$domains} --cert-name {$certificate->domains[0]} --webroot --webroot-path {$certificate->project->michmanDir}/public");
        $this->read();

        // TODO: CRITICAL! CONTINUE. Test this again, should work now. Btw, once we get the certificate - copy it from the server and save somewhere. Let's Encrypt has a pretty strict rate limiting, I already got stuck because of that.

        if ($this->failed()) {
            $this->disablePty();
            throw new ServerScriptException('The certbot certificate request has failed.');
        }

        $this->disablePty();
    }
}
