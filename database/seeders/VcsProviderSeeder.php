<?php declare(strict_types=1);

namespace Database\Seeders;

use App\DataTransferObjects\AuthTokenDto;
use App\Models\User;
use App\Models\VcsProvider;
use Illuminate\Database\Seeder;

class VcsProviderSeeder extends Seeder
{
    /** @var int Number of fake providers to seed. */
    private const NUM_PROVIDERS = 10;

    public function run(): void
    {
        // Seed an actual dev token from config.
        $dev = User::query()->firstWhere('email', (string) config('app.dev_email'));
        if (! is_null($dev)) {
            VcsProvider::factory([
                'provider' => 'github_v3',
                'token' => new AuthTokenDto('5469212', (string) config('vcs.github_dev_token')),
                'external_id' => '5469212',
                'nickname' => 'KondorB',
            ])
                ->for($dev, 'user')
                ->create();
        }

        // Seed fake tokens.
        VcsProvider::factory()
            ->forRandomUserOnceFrom(User::query()
                ->whereNotIn('email', [(string) config('app.dev_email')])
                ->get()
            )
            ->count(static::NUM_PROVIDERS)
            ->create();
    }
}
