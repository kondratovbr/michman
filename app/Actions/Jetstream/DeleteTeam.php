<?php declare(strict_types=1);

namespace App\Actions\Jetstream;

use App\Exceptions\NotImplementedException;
use App\Models\Team;
use Laravel\Jetstream\Contracts\DeletesTeams;

class DeleteTeam implements DeletesTeams
{
    /** @param Team $team */
    public function delete($team): void
    {
        throw new NotImplementedException('This action is deprecated and should not be used.');

        // $team->purge();
    }
}
