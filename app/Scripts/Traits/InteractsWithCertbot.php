<?php declare(strict_types=1);

namespace App\Scripts\Traits;

use App\Scripts\AbstractServerScript;
use App\Scripts\Exceptions\ServerScriptException;
use App\Support\Str;

/**
 * Trait InteractsWithCertbot for server scripts.
 *
 * @mixin AbstractServerScript
 */
trait InteractsWithCertbot
{
    protected function certbotReceiveCertificate(string $domain, string $userEmail): string
    {
        $this->enablePty();
        $this->setTimeout(60 * 30); // 30 min

        $publicDir = '/home/' . config('servers.worker_user') . '/public';

        $this->execPty("certbot certonly -n --expand --allow-subset-of-names -m {$userEmail} --agree-tos -d {$domain} --cert-name {$domain} --webroot --webroot-path {$publicDir}");

        $output = $this->read();

        if ($this->failed()) {
            $this->disablePty();
            throw new ServerScriptException('The certbot certificate request has failed.');
        }

        $this->disablePty();

        return $output;
    }

    protected function certbotHasCertificate(string $name): bool
    {
        $output = $this->exec('certbot -n certificates');

        return Str::containsLax($output, $name);
    }

    protected function certbotDeleteCertificate(string $name): string|bool
    {
        return $this->exec("certbot -n delete --cert-name {$name}");
    }
}
