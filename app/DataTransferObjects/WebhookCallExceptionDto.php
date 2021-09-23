<?php declare(strict_types=1);

namespace App\DataTransferObjects;

class WebhookCallExceptionDto extends AbstractDto
{
    public function __construct(
        public int $code,
        public string $message,
        public string $trace,
    ) {}
}
