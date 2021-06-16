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

            // TODO: Don't forget to implement these following seeders:
            FirewallRuleSeeder::class,
            UserSshKeySeeder::class,
            DatabaseModelSeeder::class,
            DatabaseUserSeeder::class,
            PythonSeeder::class,
            ServerSshKeySeeder::class,
            DeploySshKeySeeder::class,
            VcsProviderSeeder::class,
        ]);
    }
}
