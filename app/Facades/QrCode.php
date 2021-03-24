<?php declare(strict_types=1);

namespace App\Facades;

use SimpleSoftwareIO\QrCode\Facades\QrCode as BaseFacade;
use SimpleSoftwareIO\QrCode\Generator;

/**
 * Qr facade
 *
 * Exists only to provide some better info for an IDE.
 *
 * @method static Generator format(string $format)
 */
class QrCode extends BaseFacade
{
    //
}
