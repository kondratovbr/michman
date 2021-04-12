<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /** @var int Number of generic users to seed. */
    private const NUM_USERS = 10;

    /**
     * Seed the database.
     */
    public function run(): void
    {
        // Seed admin user.
        User::factory()->theAdmin()->withPersonalTeam()->create();

        // Seed a predefined generic user useful for development.
        User::factory()->theUser()->withPersonalTeam()->create();

        // Seed generic users.
        User::factory()->withPersonalTeam()->times(self::NUM_USERS)->create();
    }
}
