<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends AbstractController
{
    public function __invoke(Request $request)
    {
        //

        return view('testview');
    }
}
