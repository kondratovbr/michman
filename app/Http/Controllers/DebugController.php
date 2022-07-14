<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Events\Users\FlashMessageEvent;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

class DebugController extends AbstractController
{
    public function __construct()
    {
        $this->middleware('debug');
    }

    /** Show the list of registered routes of the application. */
    public function routes(): View
    {
        $routes = Route::getRoutes();

        return view('debug.routes', [
            'routes' => $routes,
        ]);
    }

    /** Show a standard phpinfo page. */
    public function phpInfo(): void
    {
        phpinfo();
    }

    /** Try websockets with a notification for the currently authed user. */
    public function websockets(): void
    {
        flash('If you see this - websockets work!', FlashMessageEvent::STYLE_SUCCESS);
    }

    /** Show a completely empty HTML page. */
    public function empty(): View
    {
        return view('debug.empty');
    }

    /** Show a blank page. */
    public function blank(): View
    {
        return view('debug.blank');
    }

    /** Show a Pusher test page. */
    public function pusher(): View
    {
        return view('debug.pusher');
    }

    /** Dump all environment values. */
    public function env(): void
    {
        dump($_ENV);
    }

    /** Dump all config values. */
    public function config(): void
    {
        dump(config()->all());
    }

    /** Throw an exception. */
    public function exception(): void
    {
        throw new Exception('This is a blank test exception.');
    }

    /** Send a test email to the admin. */
    public function email(): void
    {
        $address = config('app.admin_email');

        $mail = new class extends Mailable {
            public function build(): static
            {
                return $this
                    ->from(config('mail.from'))
                    ->subject('Test Email')
                    ->html(
                        '<p>This is a test email from ' .
                        config('app.name') .
                        '.<p><p>If can you see this - emailing works!</p>'
                    );
            }
        };

        Mail::to($address)->send($mail);
    }
}
