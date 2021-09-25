<?php declare(strict_types=1);

namespace App\Notifications\Daemons;

use App\Models\Daemon;
use App\Models\User;
use App\Notifications\AbstractNotification;

abstract class AbstractDaemonNotification extends AbstractNotification
{
    public function __construct(
        protected Daemon $daemon,
    ) {
        parent::__construct();
    }

    public function toArray(User $notifiable): array
    {
        return [
            'daemonKey' => $this->daemon->getKey(),
        ];
    }

    /** Retrieve the daemon model from the database. */
    protected static function daemon(array $data): Daemon|null
    {
        /** @var Daemon|null $daemon */
        $daemon = Daemon::query()->find($data['daemonKey']);

        return $daemon;
    }

    protected static function dataForMessage(array $data = []): array
    {
        return [
            'server' => static::daemon($data)->server->name,
        ];
    }
}
