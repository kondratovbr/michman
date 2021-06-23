<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Python;
use App\Models\Server;
use Illuminate\Database\Seeder;

class PythonSeeder extends Seeder
{
    /** @var int Fraction of existing servers to create Python models for. */
    private const FRACTION = 0.75;

    /**
     * Seed the database.
     */
    public function run(): void
    {
        $servers = Server::query()
            ->whereIn('type', ['app', 'web', 'worker'])
            ->get();

        Python::factory()
            ->forRandomServerFromCollectionOnce($servers)
            ->count((int) ceil($servers->count() * self::FRACTION))
            ->create();
    }
}
