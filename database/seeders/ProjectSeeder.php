<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /** @var int Number of fake projects to seed. */
    private const NUM_PROJECTS = 10;

    public function run(): void
    {
        Project::factory()
            ->forRandomUserFromCollection(User::query()->whereHas('servers')->get())
            ->count(self::NUM_PROJECTS)
            ->create();
    }
}
