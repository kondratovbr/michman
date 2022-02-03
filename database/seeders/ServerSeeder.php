<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Provider;
use App\Models\Server;
use App\Models\User;
use Illuminate\Database\Seeder;

class ServerSeeder extends Seeder
{
    /** @var int Number of fake servers to seed. */
    private const NUM_SERVERS = 10;

    public function run(): void
    {
        // Make sure the dev user has a server seeded.
        $dev = User::query()->firstWhere('email', (string) config('app.dev_email'));
        if ($dev) {
            Server::factory()
                ->for($dev->providers()->first())
                ->create();
        }

        // Make sure the generic user has a server seeded as well.
        $user = User::query()->firstWhere('email', 'user@example.com');
        if ($user) {
            Server::factory()
                ->for($user->providers()->first())
                ->create();
        }

        // Seed the rest of the servers.
        Server::factory()
            ->forRandomProviderFrom(Provider::all())
            ->count(static::NUM_SERVERS)
            ->create();
    }
}
