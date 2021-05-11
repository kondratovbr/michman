<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Provider;
use App\Models\Server;
use Illuminate\Database\Seeder;

class ServerSeeder extends Seeder
{
    /** @var int Number of fake servers to seed. */
    private const NUM_SERVERS = 10;

    /**
     * Seed the database.
     */
    public function run(): void
    {
        Server::factory()
            ->forRandomProviderFrom(Provider::all())
            ->count(static::NUM_SERVERS)
            ->create();
    }
}
