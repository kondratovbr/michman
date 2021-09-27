<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Project;
use App\Models\Webhook;
use Illuminate\Database\Eloquent\Factories\Factory;

class WebhookFactory extends Factory
{
    protected $model = Webhook::class;

    public function definition(): array
    {
        return [
            'provider' => 'github',
            'type' => 'push',
            'url' => 'http://localhost/',
            'secret' => '1234567890',
            'state' => 'enabling',
        ];
    }

    /** @return $this */
    public function withProject(): static
    {
        return $this->for(Project::factory()->withUserAndServers());
    }

    /** @return $this */
    public function enabled(): static
    {
        return $this->state([
            'state' => 'enabled',
            'external_id' => 123,
        ]);
    }

    /** @return $this */
    public function inState(string $state): static
    {
        return $this->state([
            'state' => $state,
        ]);
    }
}
