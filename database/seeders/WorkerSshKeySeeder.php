<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Server;
use App\Models\WorkerSshKey;
use Illuminate\Database\Seeder;

class WorkerSshKeySeeder extends Seeder
{
    public function run(): void
    {
        Server::all()->each(fn(Server $server) =>
            WorkerSshKey::factory()->for($server)->create()
        );
    }
}
