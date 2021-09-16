<?php declare(strict_types=1);

namespace App\DataTransferObjects;

class WebhookDto extends AbstractDto
{
    public function __construct(
        /** @var string[] */
        public array $events,
        public string|null $id = null,
    ) {}
}
