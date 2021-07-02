<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DatabaseUser;
use App\Models\Server;
use Illuminate\Database\Seeder;

class DatabaseUserSeeder extends Seeder
{
    /** @var int Total number of database users to seed. */
    protected const NUM_DATABASE_USERS = 50;

    /**
     * Seed the database.
     */
    public function run(): void
    {
        // Make sure something is seeded for the first server with which I'm usually working during development.
        DatabaseUser::factory()
            ->for(Server::first())
            ->attachToRandomDatabase()
            ->count(3)
            ->create();

        // Seed generic database users.
        DatabaseUser::factory()
            ->forRandomServerFrom(Server::all())
            ->count(static::NUM_DATABASE_USERS)
            ->create();
    }
}
