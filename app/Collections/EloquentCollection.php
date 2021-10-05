<?php declare(strict_types=1);

namespace App\Collections;

use App\Models\Interfaces\HasTasksCounterInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EloquentCollection extends Collection
{
    /** Increment "tasks" attribute on every model in this collection. */
    public function incrementTasks(int $amount = 1): void
    {
        DB::transaction(function () use ($amount) {
            foreach ($this as $item) {
                if ($item instanceof HasTasksCounterInterface)
                    $item->incrementTasks($amount);
            }
        }, 5);
    }

    /** Decrement "tasks" attribute on every model in this collection. */
    public function decrementTasks(int $amount = 1): void
    {
        DB::transaction(function () use ($amount) {
            foreach ($this as $item) {
                if ($item instanceof HasTasksCounterInterface)
                    $item->decrementTasks($amount);
            }
        }, 5);
    }

    public function firstWhereMax($key): mixed
    {
        return $this->firstWhere($key, $this->max($key));
    }
}
