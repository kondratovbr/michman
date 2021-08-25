<?php declare(strict_types=1);

namespace App\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

class DeploymentQueryBuilder extends Builder
{
    /**
     * Query only finished deployments.
     *
     * @return $this
     */
    public function finished(): static
    {
        return $this->whereDoesntHave('servers', function (Builder $query) {
            $query->whereNull('deployment_server.finished_at');
        });
    }

    /**
     * Query only successful deployments.
     *
     * @return $this
     */
    public function successful(): static
    {
        return $this->finished()
            ->whereDoesntHave('servers', function (Builder $query) {
                $query->where('deployment_server.successful', false);
            });
    }
}
