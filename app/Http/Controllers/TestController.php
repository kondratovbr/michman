<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Mail\WelcomeEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TestController extends AbstractController
{
    public function __invoke(Request $request)
    {
        Mail::to(user())->send(
            new WelcomeEmail(user()),
        );

        return view('debug.blank');
    }
}
