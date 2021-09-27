<?php declare(strict_types=1);

namespace App\States\Certificates;

class Installed extends CertificateState
{
    public static string $name = 'installed';
    public static string $colors = 'success';
}
