<?php declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasModelHelpers;
use App\Models\Traits\UsesCamelCaseAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Customized base model class for Eloquent models
 */
abstract class AbstractModel extends Model
{
    use UsesCamelCaseAttributes,
        HasModelHelpers;
}
