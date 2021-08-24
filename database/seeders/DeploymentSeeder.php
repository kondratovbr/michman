<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Deployment;
use App\Models\Project;
use Illuminate\Database\Seeder;

class DeploymentSeeder extends Seeder
{
    /** @var int Number of deployments to seed. */
    const NUM_DEPLOYMENTS=20;

    /**
     * Seed the database.
     */
    public function run(): void
    {
        Deployment::factory()
            ->forRandomProjectFrom(Project::all())
            ->count(self::NUM_DEPLOYMENTS)
            ->create();
    }
}
