<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DeploySshKey;
use App\Models\Project;
use Illuminate\Database\Seeder;

class DeploySshKeySeeder extends Seeder
{
    public function run(): void
    {
        DeploySshKey::factory()
            ->forRandomProjectFromCollectionOnce(Project::all())
            ->count(Project::count())
            ->create();
    }
}
