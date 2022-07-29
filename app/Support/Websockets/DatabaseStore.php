<?php declare(strict_types=1);

namespace App\Support\Websockets;

use BeyondCode\LaravelWebSockets\Statistics\Stores\DatabaseStore as BaseDatabaseStore;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

class DatabaseStore extends BaseDatabaseStore
{
    /**
     * A customized model that will interact with the database.
     *
     * @var string
     */
    public static $model = StatisticsEntry::class;

    /**
     * Convert CarbonImmutable provided into a Carbon instance required by parent DatabaseStore.
     *
     * @param string|int|null $appId
     */
    public static function delete(CarbonInterface $moment, $appId = null): int
    {
        if ($moment instanceof CarbonImmutable)
            $moment = new Carbon($moment);

        return parent::delete($moment, $appId);
    }
}
