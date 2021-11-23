<?php declare(strict_types=1);

namespace App\Handlers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class AbstractHandler
{
    use AuthorizesRequests;
    use ValidatesRequests;
}
