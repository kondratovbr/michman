<?php declare(strict_types=1);

namespace App\Facades;

use App\Support\ConfigViewFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Facade;

/**
 * Facade to render Blade-templated config files.
 *
 * @method static string render(string $view, array $data = [])
 * @method static View make(string $view, array $data = [])
 *
 * @see ConfigViewFactory
 */
class ConfigView extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'config-view-factory';
    }
}
