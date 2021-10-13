<?php declare(strict_types=1);

namespace App\Notifications\Workers;

use App\Models\User;
use App\Models\Worker;
use App\Notifications\AbstractNotification;

abstract class AbstractWorkerNotification extends AbstractNotification
{
    public function __construct(
        protected Worker $worker,
    ) {
        parent::__construct();
    }

    public function toArray(User $notifiable): array
    {
        return [
            'workerKey' => $this->worker->getKey(),
        ];
    }

    /** Retrieve the worker model from the database. */
    protected static function worker(array $data): Worker|null
    {
        /** @var Worker|null $worker */
        $worker = Worker::query()->find($data['workerKey']);

        return $worker;
    }

    protected static function dataForMessage(array $data = []): array
    {
        return [
            'project' => static::worker($data)->project->domain,
        ];
    }
}
