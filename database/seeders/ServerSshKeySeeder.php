<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Server;
use App\Models\ServerSshKey;
use Illuminate\Database\Seeder;

class ServerSshKeySeeder extends Seeder
{
    /** @var int Fraction of existing servers to create server SSH keys for. */
    private const FRACTION = 0.5;

    /**
     * Seed the database.
     */
    public function run(): void
    {
        ServerSshKey::factory()
            ->forRandomServerFromCollectionOnce(Server::all())
            ->count((int) ceil(Server::count() * self::FRACTION))
            ->create();
    }
}
