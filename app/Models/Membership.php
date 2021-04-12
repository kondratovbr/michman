<?php declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasModelHelpers;
use App\Models\Traits\UsesCamelCaseAttributes;
use Laravel\Jetstream\Membership as JetstreamMembership;

class Membership extends JetstreamMembership
{
    use UsesCamelCaseAttributes,
        HasModelHelpers;

    /** @var bool Indicates if the IDs are auto-incrementing. */
    public $incrementing = true;
}
