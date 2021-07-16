<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Servers\StoreServerAction;
use App\Models\Server;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use phpseclib3\Net\SFTP;

class TestController extends AbstractController
{
    public function __invoke(Request $request, StoreServerAction $storeServer)
    {
        /** @var Server $server */
        $server = Server::query()
            ->where('name', 'dried-grease')
            ->firstOrFail();

        dd(
            $server->databaseRootPassword,
        );

        die();

        return view('debug.blank');
    }
}
