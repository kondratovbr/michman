<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Servers\StoreServerAction;
use Illuminate\Http\Request;

class TestController extends AbstractController
{
    public function __invoke(Request $request, StoreServerAction $storeServer)
    {
        //

        return view('debug.blank');
    }
}
