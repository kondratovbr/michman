<?php declare(strict_types=1);

namespace App\Console\Commands\Users;

use App\Console\Commands\AbstractCommand;
use App\Console\Commands\Traits\ProtectedOnProduction;
use App\Mail\WelcomeEmail;
use App\Models\User;
use App\Services\MailerLite;
use Illuminate\Support\Facades\Mail;

class SyncUsersToMailingService extends AbstractCommand
{
    use ProtectedOnProduction;

    /** @var string The name and signature of the console command. */
    protected $signature = 'users:send-welcome-emails';

    /** @var string The console command description. */
    protected $description = 'Send welcome emails to all existing users who have verified emails.';

    /**
     * Perform the console command.
     */
    public function perform(MailerLite $mailer): int
    {
        $users = User::query()
            ->whereNotNull('email_verified_at')
            ->lazy();

        /** @var User $user */
        foreach ($users as $user) {
            $mailer->upsertSubscriber($user->email);
        }

        return 0;
    }
}
