<?php declare(strict_types=1);

namespace App\Console\Commands\Users;

use App\Console\Commands\AbstractCommand;
use App\Console\Commands\Traits\ProtectedOnProduction;
use App\Models\User;
use App\Services\MailerLite;

class SyncUsersToMailingService extends AbstractCommand
{
    use ProtectedOnProduction;

    /** @var string The name and signature of the console command. */
    protected $signature = 'michman:users:sync-to-mailer';

    /** @var string The console command description. */
    protected $description = 'Sync users with verified emails into a mailing service.';

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
