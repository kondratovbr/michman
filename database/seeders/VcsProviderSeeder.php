<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\VcsProvider;
use Illuminate\Database\Seeder;

class VcsProviderSeeder extends Seeder
{
    /** @var int Number of fake providers to seed. */
    private const NUM_PROVIDERS = 10;

    /**
     * Seed the database.
     */
    public function run(): void
    {
        VcsProvider::factory()
            ->forRandomUserFrom(User::all())
            ->count(static::NUM_PROVIDERS)
            ->create();
    }
}
