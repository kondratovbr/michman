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
        $this->call([
            UserSeeder::class,
            ProviderSeeder::class,
            ServerSeeder::class,
            WorkerSshKeySeeder::class,
            VcsProviderSeeder::class,
            FirewallRuleSeeder::class,
            DatabaseModelSeeder::class,
            DatabaseUserSeeder::class,

            // TODO: Don't forget to implement these following seeders:
            PythonSeeder::class,
            UserSshKeySeeder::class,
            ServerSshKeySeeder::class,
            DeploySshKeySeeder::class,
            DeploymentSeeder::class,
        ]);
    }
}
