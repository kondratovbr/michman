<?php declare(strict_types=1);

namespace App\Jobs\Middleware;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\Middleware\WithoutOverlapping;

class WithoutOverlappingOnModel extends WithoutOverlapping
{
    private Model $lockModel;

    public function __construct(Model $model)
    {
        parent::__construct($model->getKey());

        $this->lockModel = $model->withoutRelations();
    }

    /**
     * Override the built-in method to use the model class instead of a job class as a lock key,
     * allowing to run only one job interacting with this model at the same time.
     */
    public function getLockKey($job): string
    {
        return $this->prefix . get_class($this->lockModel) . ':' . $this->key;
    }
}
