<?php declare(strict_types=1);

namespace App\Notifications\Servers;

use App\Models\Server;
use App\Models\User;
use App\Notifications\AbstractNotification;

abstract class AbstractServerNotification extends AbstractNotification
{
    public function __construct(
        protected Server $server,
    ) {
        parent::__construct();
    }

    public function toArray(User $notifiable): array
    {
        return [
            'serverKey' => $this->server->getKey(),
        ];
    }

    /**
     * Retrieve the server from the database.
     */
    protected static function deployment(array $data): Server|null
    {
        /** @var Server|null $server */
        $server = Server::query()->find($data['serverKey']);

        return $server;
    }
}
