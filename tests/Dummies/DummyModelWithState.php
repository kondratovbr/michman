<?php declare(strict_types=1);

namespace Tests\Dummies;

use App\Models\AbstractModel;
use Spatie\ModelStates\HasStates;
use Tests\Dummies\DummyStates\DummyState;

/**
 * @property DummyState $state
 */
class DummyModelWithState extends AbstractModel
{
    use HasStates;

    protected $fillable = [
        'state',
    ];

    protected $hidden = [];

    protected $casts = [
        'state' => DummyState::class,
    ];
}
