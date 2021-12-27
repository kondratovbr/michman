<?php declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;

class TestNotification extends AbstractNotification
{
    public function __construct(
        public string $message = 'Test notification',
    ) {
        parent::__construct();
    }

    public function toArray(User $notifiable): array
    {
        return [
            'message' => $this->message,
        ];
    }

    protected static function dataForMessage(array $data = []): array
    {
        return $data;
    }
}
