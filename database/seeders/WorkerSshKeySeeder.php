<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Server;
use App\Models\WorkerSshKey;
use Illuminate\Database\Seeder;

class WorkerSshKeySeeder extends Seeder
{
    /** @var int Fraction of existing servers to create SSH keys for. */
    private const FRACTION = 0.5;

    /**
     * Seed the database.
     */
    public function run(): void
    {
        WorkerSshKey::factory()
            ->forRandomServerFromCollectionOnce(Server::all())
            ->count((int) ceil(Server::count() * self::FRACTION))
            ->create();
    }
}
