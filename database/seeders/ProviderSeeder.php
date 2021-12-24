<?php declare(strict_types=1);

namespace Database\Seeders;

use App\DataTransferObjects\AuthTokenDto;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    /** @var int Number of fake providers to seed. */
    private const NUM_PROVIDERS = 10;

    public function run(): void
    {
        // Seed an actual dev token from config.
        $dev = User::query()->firstWhere('email', (string) config('app.dev_email'));
        if (! is_null($dev)) {
            Provider::factory([
                'provider' => 'digital_ocean_v2',
                'token' => new AuthTokenDto(
                    '123456',
                    (string) config('providers.do_dev_token'),
                ),
                'name' => 'Dev Token',
            ])
                ->for($dev, 'user')
                ->create();
        }

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
