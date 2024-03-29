<?php declare(strict_types=1);

namespace App\Models\Traits;

use App\Support\Arr;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

// TODO: IMPORTANT! Cover this with tests! Also - incrementing and decrementing are very similar. Should DRY it out.

/**
 * Trait HasTasksCounter for Eloquent models
 *
 * @mixin Model
 */
trait HasTasksCounter
{
    /** @var string The name of the tasks counter attribute in the database. */
    protected string $tasksAttributeName = 'tasks';

    public function getTasksAttribute(): int
    {
        if (is_null($this->attributes[$this->tasksAttributeName] ?? null))
            return 0;

        return (int) $this->attributes[$this->tasksAttributeName];
    }

    public function setTasksAttribute(int|null $value): void
    {
        $this->attributes[$this->tasksAttributeName] = $value ?? 0;
    }

    public function tasksAttributeName(): string
    {
        return $this->tasksAttributeName;
    }

    public function incrementTasks(int $amount = 1): void
    {
        // We're saving the timestamp to be absolutely sure that the timestamp
        // in the DB and the one in this instance will end up exactly the same.
        $now = now();

        /*
         * We don't have to reload or lock the model here
         * because this is a safe query that goes like this:
         *
         * UPDATE
         *     `databases`
         * SET
         *     `tasks` = `tasks` + 1,
         *     `databases`.`updated_at` = `2021-07-08 10:37:49`
         * WHERE
         *     `databases`.`id` = 1
         *
         * 'updated_at' may be updated to a slightly lower value, which
         * isn't critical and shouldn't be an issue.
         */
        $updated = $this->newQuery()
            ->whereKey($this->getKey())
            ->increment(
                $this->tasksAttributeName(),
                $amount,
                $this->usesTimestamps()
                    ? [$this->getUpdatedAtColumn() => $now]
                    : [],
            );

        if ($updated) {
            $this->incrementAttributes($amount);
            $this->setUpdatedAtAttributes($now);
            $this->fireUpdatedEvent();
        } else {
            $this->logFailedQueryWarning();
        }
    }

    public function decrementTasks(int $amount = 1): void
    {
        // See comments on incrementTasks method for details.

        $now = now();

        $updated = $this->newQuery()
            ->whereKey($this->getKey())
            ->decrement(
                $this->tasksAttributeName(),
                $amount,
                $this->usesTimestamps()
                    ? [$this->getQualifiedUpdatedAtColumn() => $now]
                    : [],
            );

        if ($updated) {
            $this->decrementAttributes($amount);
            $this->setUpdatedAtAttributes($now);
            $this->fireUpdatedEvent();
        } else {
            $this->logFailedQueryWarning();
        }
    }

    /** Check if there is any tasks pending for this model. */
    public function hasTasks(): bool
    {
        return $this->tasks > 0;
    }

    /** Fire the "updated" if this model has it declared. */
    private function fireUpdatedEvent(): void
    {
        // The model wasn't "saved" here, but it was "updated",
        // so we only fire the "updated" event.
        if ($event = Arr::get($this->dispatchesEvents,'updated'))
            event(new $event($this));
    }

    /** Log a warning that the update query has failed. */
    private function logFailedQueryWarning(): void
    {
        Log::warning(
            'Query to update the task counter on a model returned 0 (no records were updated). Class: '
            . $this::class
            . ', key: '
            . $this->getKey()
        );
    }

    /** Set "updated_at" column on this model instance attributes including "original" one. */
    private function setUpdatedAtAttributes(CarbonInterface $timestamp): void
    {
        if (! $this->usesTimestamps())
            return;

        $this->attributes[$this->getUpdatedAtColumn()] = $timestamp;
        // We're doing this after we updated this value in the DB,
        // so we should set the "original" as well.
        $this->original[$this->getUpdatedAtColumn()] = $timestamp;
    }

    /** Increment "tasks" attribute including the original one. */
    private function incrementAttributes(int $amount): void
    {
        // We're doing this after we updated this value in the DB,
        // so we should set the "original" as well as "attributes".

        if (is_null($this->original[$this->tasksAttributeName()] ?? null))
            $this->original[$this->tasksAttributeName()] = 0;

        $this->original[$this->tasksAttributeName()] += $amount;

        $this->attributes[$this->tasksAttributeName()] = $this->original[$this->tasksAttributeName()];
    }

    /** Decrement "tasks" attribute including the original one. */
    private function decrementAttributes(int $amount): void
    {
        // We're doing this after we updated this value in the DB,
        // so we should set the "original" as well as "attributes".

        if (is_null($this->original[$this->tasksAttributeName()] ?? null))
            $this->original[$this->tasksAttributeName()] = 0;
        else
            $this->original[$this->tasksAttributeName()] -= $amount;

        if ($this->original[$this->tasksAttributeName()] < 0) {
            Log::warning('The "tasks" attribute value went below zero. Class: ' . $this::class . ', key: ' . $this->getKey());
            $this->original[$this->tasksAttributeName()] = 0;
        }

        $this->attributes[$this->tasksAttributeName()] = $this->original[$this->tasksAttributeName()];
    }
}
