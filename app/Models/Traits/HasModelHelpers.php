<?php declare(strict_types=1);

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * Trait HasModelHelpers
 *
 * @mixin Model
 */
trait HasModelHelpers
{
    /**
     * Get the name of a database table used by this model class.
     */
    public static function tableName(): string
    {
        return (new static)->getTable();
    }
}
