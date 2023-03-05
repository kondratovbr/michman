<?php declare(strict_types=1);

namespace App\Services;

use App\Services\LogSnag\SnagChannel;
use App\Services\LogSnag\SnagEvent;
use App\Support\Arr;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class LogSnag
{
    private const BASE_URL = 'https://api.logsnag.com/v1';
    private const PROJECT_NAME = 'michman';

    public function __construct(
        private readonly string $token,
    ) {}

    public function publishEvent(
        SnagChannel $channel,
        SnagEvent $event,
        string|null $description = null,
        string|null $icon = null,
        bool $notify = false,
    ): void {
        $this->request()->post(
            url: "/log",
            data: Arr::whereNotEmpty([
                'project' => static::PROJECT_NAME,
                'channel' => $channel->value,
                'event' => $event->value,
                'description' => $description,
                'icon' => $icon,
                'notify' => $notify,
            ]),
        );
    }

    private function request(): PendingRequest
    {
        return Http::withToken($this->token)
            ->baseUrl(static::BASE_URL)
            ->asJson()
            ->acceptJson();
    }
}
