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

            // TODO: Don't forget to implement.
            FirewallRuleSeeder::class,
            UserSshKeySeeder::class,
        ]);
    }
}
