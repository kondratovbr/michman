<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Webhook;
use App\Models\WebhookCall;
use Illuminate\Database\Eloquent\Factories\Factory;

class WebhookCallFactory extends Factory
{
    protected $model = WebhookCall::class;

    public function definition(): array
    {
        return [
            'type' => 'push',
            'url' => 'http://localhost/',
            'external_id' => '123',
            'processed' => true,
        ];
    }

    /** @return $this */
    public function withWebhook(): static
    {
        return $this->for(Webhook::factory()->withProject());
    }
}
