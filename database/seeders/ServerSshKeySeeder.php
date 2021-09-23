<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Server;
use App\Models\ServerSshKey;
use Illuminate\Database\Seeder;

class ServerSshKeySeeder extends Seeder
{
    public function run(): void
    {
        ServerSshKey::factory()
            ->forRandomServerFromCollectionOnce(Server::all())
            ->count(Server::count())
            ->create();
    }
}
