<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\Certificate;
use App\Models\Server;
use Illuminate\Database\Eloquent\Factories\Factory;

class CertificateFactory extends Factory
{
    protected $model = Certificate::class;

    public function definition(): array
    {
        return [
            'type' => 'lets-encrypt',
            'domain' => $this->faker->domainName,
            'state' => 'installing',
        ];
    }

    /** @return $this */
    public function withServer(): static
    {
        return $this->for(Server::factory()->withProvider());
    }

    /** @return $this */
    public function inState(string $state): static
    {
        return $this->state([
            'state' => $state,
        ]);
    }
}
