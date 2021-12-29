<?php declare(strict_types=1);

namespace App\Scripts\Traits;

use Illuminate\Support\Facades\App;

trait RunsStatically
{
    /** Create an instance of this script and execute it using the arguments provided. */
    public static function run(...$arguments): mixed
    {
        return App::make(static::class)->execute(...$arguments);
    }
}
