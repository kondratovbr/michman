<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // TODO: CRITICAL! CONTINUE. Some of my latest updates to seeders/factories don't work and seeding fails.

        $this->call([
            UserSeeder::class,
            ProviderSeeder::class,
            ServerSeeder::class,
            ServerSshKeySeeder::class,
            WorkerSshKeySeeder::class,
            VcsProviderSeeder::class,
            FirewallRuleSeeder::class,
            DatabaseModelSeeder::class,
            DatabaseUserSeeder::class,

            // TODO: Don't forget to implement these following seeders:
            PythonSeeder::class,
            UserSshKeySeeder::class,

            ProjectSeeder::class,
            DeploySshKeySeeder::class,
            
            DeploymentSeeder::class,
        ]);
    }
}
