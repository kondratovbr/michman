<?php declare(strict_types=1);

namespace App\Validation\Fields;

use App\Validation\AbstractBaseRules;

abstract class AbstractField extends AbstractBaseRules
{
    public static function new(): static
    {
        return new static;
    }
}
