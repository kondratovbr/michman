<?php declare(strict_types=1);

namespace App\Collections;

use App\Models\AbstractModel;
use Illuminate\Database\Eloquent\Collection;

class EloquentCollection extends Collection
{
    /**
     * Update statuses on all models in this collection.
     */
    public function updateStatus(string $status, string $statusColumnName = 'status', string $keyName = 'id'): int
    {
        if ($this->isEmpty())
            return 0;

        /** @var AbstractModel $model */
        $model = $this->first();

        return $model->newQuery()
            ->whereKey($this->pluck($keyName))
            ->update([$statusColumnName => $status]);
    }
}
