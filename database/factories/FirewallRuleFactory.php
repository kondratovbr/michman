<?php declare(strict_types=1);

namespace Database\Factories;

use App\Models\FirewallRule;
use App\Models\Server;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

class FirewallRuleFactory extends Factory
{
    /** @var string The name of the factory's corresponding model. */
    protected $model = FirewallRule::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->domainName,
            'port' => (string) rand(1, 1024),
            'from_ip' => rand(0, 1) ? $this->faker->ipv4 : null,
            'can_delete' => true,
            'status' => FirewallRule::STATUS_ADDED,
        ];
    }

    /**
     * Attach firewall rules to random servers from a collection.
     *
     * @return $this
     */
    public function forRandomServerFrom(Collection $servers): static
    {
        return $this->afterMaking(
            fn (FirewallRule $rule) => $this->associateServer($rule, $servers->random())
        );
    }

    /**
     * Attach a firewall rule to a server.
     *
     * @return $this
     */
    protected function associateServer(FirewallRule $rule, Server $server): void
    {
        $rule->server()->associate($server);
    }
}
