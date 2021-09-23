<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Database;
use App\Models\Server;
use Illuminate\Database\Seeder;

class DatabaseModelSeeder extends Seeder
{
    /** @var int Total number of databases to seed. */
    protected const NUM_DATABASES = 30;

    public function run(): void
    {
        // Make sure something is seeded for the first server with which I'm usually working during development.
        Database::factory()
            ->for(Server::first())
            ->count(3)
            ->create();

        // Seed generic databases.
        Database::factory()
            ->forRandomServerFrom(Server::all())
            ->count(static::NUM_DATABASES)
            ->create();
    }
}
