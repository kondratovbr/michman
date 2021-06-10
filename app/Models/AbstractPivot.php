<?php declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\UsesCamelCaseAttributes;
use Illuminate\Database\Eloquent\Relations\Pivot;

abstract class AbstractPivot extends Pivot
{
    use UsesCamelCaseAttributes;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    //
}
