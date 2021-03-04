<?php declare(strict_types=1);

namespace App\Actions\Jetstream;

use Laravel\Jetstream\Contracts\DeletesTeams;

class DeleteTeam implements DeletesTeams
{
    /**
     * Delete the given team.
     */
    public function delete($team): void
    {
        $team->purge();
    }
}
