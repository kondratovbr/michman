<?php declare(strict_types=1);

namespace App\Console\Commands\Users;

use App\Console\Commands\AbstractCommand;
use App\Console\Commands\Traits\ProtectedOnProduction;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmailsCommand extends AbstractCommand
{
    use ProtectedOnProduction;

    /** @var string The name and signature of the console command. */
    protected $signature = 'users:send-welcome-emails';

    /** @var string The console command description. */
    protected $description = 'Send welcome emails to all existing users who have verified emails.';

    /**
     * Perform the console command.
     */
    public function perform(): int
    {
        $users = User::query()
            ->whereNotNull('email_verified_at')
            ->lazy();

        /** @var User $user */
        foreach ($users as $user) {

            $this->line("Queuing email for User $user->id to $user->email");

            Mail::to($user)->send(new WelcomeEmail);

        }

        return 0;
    }
}
