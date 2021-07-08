<?php declare(strict_types=1);

namespace App\Models\Interfaces;

use App\Models\AbstractModel;

/**
 * Interface HasTasksCounterInterface for Eloquent models
 *
 * @property int $tasks
 *
 * @mixin AbstractModel
 */
interface HasTasksCounterInterface
{
    public function getTasksAttribute(): int;

    public function setTasksAttribute(int $value): void;

    /**
     * Get the tasks DB column name.
     */
    public function tasksAttributeName(): string;

    /**
     * Increment the tasks counter attribute on this model.
     */
    public function incrementTasks(int $amount = 1): void;

    /**
     * Decrement the tasks counter attribute on this model.
     */
    public function decrementTasks(int $amount = 1): void;
}
