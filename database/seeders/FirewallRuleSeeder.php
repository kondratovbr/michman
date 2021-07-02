<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\FirewallRule;
use App\Models\Server;
use Illuminate\Database\Seeder;

class FirewallRuleSeeder extends Seeder
{
    /** @var int Total number of firewall rules to seed. */
    protected const NUM_RULES = 50;

    /**
     * Seed the database.
     */
    public function run(): void
    {
        // Make sure something is seeded for the first server with which I'm usually working during development.
        FirewallRule::factory()
            ->for(Server::first())
            ->count(3)
            ->create();

        // Seed generic firewall rules.
        FirewallRule::factory()
            ->forRandomServerFrom(Server::all())
            ->count(static::NUM_RULES)
            ->create();
    }
}
