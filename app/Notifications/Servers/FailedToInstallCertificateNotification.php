<?php declare(strict_types=1);

namespace App\Notifications\Servers;

use App\Models\Server;
use App\Models\User;
use App\Notifications\Interfaces\Viewable;
use Carbon\CarbonInterface;
use Illuminate\Contracts\View\View;
use RuntimeException;

class FailedToInstallCertificateNotification extends AbstractServerNotification implements Viewable
{
    public function __construct(
        protected Server $server,
        protected CarbonInterface|string|null $logFrom,
        protected CarbonInterface|string|null $logTo,
    ) {
        if (! empty($this->logFrom))
            $this->logTo ??= now();

        parent::__construct($server);
    }

    public function toArray(User $notifiable): array
    {
        return [
            'serverKey' => $this->server->getKey(),
            'logFrom' => $this->logFrom,
            'logTo' => $this->logTo,
        ];
    }

    public static function view(array $data = []): View
    {
        $server = static::server($data);

        if (is_null($server))
            throw new RuntimeException('FailedToInstallCertificateNotification exists but no server found.');

        return view('notifications.failed-to-install-certificate', [
            'server' => $server,
            'logs' => $server->getLogs($data['logFrom'], $data['logTo']),
        ]);
    }
}
