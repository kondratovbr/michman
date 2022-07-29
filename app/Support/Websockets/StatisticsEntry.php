<?php declare(strict_types=1);

namespace App\Support\Websockets;

use BeyondCode\LaravelWebSockets\Models\WebSocketsStatisticsEntry;

class StatisticsEntry extends WebSocketsStatisticsEntry
{
    // TODO: DEPLOYMENT. Persist this in an entirely separate database if needed.
    // protected $table = 'websockets_statistics_entries';
}
