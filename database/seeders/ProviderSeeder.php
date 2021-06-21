<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    /** @var int Number of fake providers to seed. */
    private const NUM_PROVIDERS = 10;

    /**
     * Seed the database.
     */
    public function run(): void
    {
        // Seed an actual dev token from config.
        Provider::factory([
            'provider' => 'digital_ocean_v2',
            'token' => (string) config('providers.do_dev_token'),
            'name' => 'Dev Token',
        ])
            ->for(
                User::query()->firstWhere('email', (string) config('app.dev_email')),
                'owner'
            )
            ->create();

        // Seed fake tokens.
        Provider::factory()
            ->forRandomUserFrom(User::query()
                ->whereNotIn('email', [(string) config('app.dev_email')])
                ->get()
            )
            ->count(static::NUM_PROVIDERS)
            ->create();
    }
}
