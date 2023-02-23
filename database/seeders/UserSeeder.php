<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /** @var int Number of generic users to seed. */
    private const NUM_USERS = 10;

    private const SEED_DEV_USER = true;

    public function run(): void
    {
        // Seed admin user.
        User::factory()
            ->theAdmin()
            ->withPersonalTeam()
            ->create();

        // Seed predefined generic users useful for development.
        User::factory()
            ->theUser()
            ->withPersonalTeam()
            ->create();

        User::factory()
            ->theEmptyOne()
            ->withPersonalTeam()
            ->create();

        if (static::SEED_DEV_USER) {
            // Seed a user with an actual email that will have actual API tokens and everything.
            /** @var User $user */
            $user = User::factory([
                'email' => 'kondratovbr@gmail.com',
                'password' => null,
            ])
                ->withPersonalTeam()
                ->withSubscription((int) env('SPARK_UNLIMITED_MONTHLY_PLAN'))
                ->create();

            $user->oauthUsers()->create([
                'provider' => 'github',
                'oauth_id' => '5469212',
                'nickname' => 'kondorb',
            ]);
        }

        // Seed generic users.
        User::factory()->withPersonalTeam()->times(self::NUM_USERS)->create();
    }
}
