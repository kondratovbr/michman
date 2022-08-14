<?php declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasModelHelpers;
use App\Models\Traits\UsesCamelCaseAttributes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\TeamInvitation as JetstreamTeamInvitation;

/**
 * @mixin IdeHelperTeamInvitation
 */
class TeamInvitation extends JetstreamTeamInvitation
{
    use UsesCamelCaseAttributes;
    use HasModelHelpers;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'email',
        'role',
    ];

    /** Get the team that the invitation belongs to. */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Jetstream::teamModel());
    }
}
