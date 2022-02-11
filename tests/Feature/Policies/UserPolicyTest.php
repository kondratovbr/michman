<?php

namespace Tests\Feature\Policies;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Hash;
use Tests\AbstractFeatureTest;

class UserPolicyTest extends AbstractFeatureTest
{
    public function test_successful_enable_tfa_action()
    {
        /** @var User $user */
        $user = User::factory([
            'password' => Hash::make('password'),
        ])->withPersonalTeam()->create();

        /** @var UserPolicy $policy */
        $policy = $this->app->make(UserPolicy::class);

        $result = $policy->enableTfa($user, $user);

        $this->assertTrue($result);
    }
    
    public function test_enable_tfa_action_for_user_that_does_not_use_password()
    {
        /** @var User $user */
        $user = User::factory([
            'password' => null,
        ])->withPersonalTeam()->viaGithub()->create();

        /** @var UserPolicy $policy */
        $policy = $this->app->make(UserPolicy::class);

        $result = $policy->enableTfa($user, $user);

        $this->assertFalse($result);
    }

    public function test_enable_tfa_action_for_different_user()
    {
        /** @var User $subject */
        $subject = User::factory([
            'password' => Hash::make('password'),
        ])->withPersonalTeam()->create();
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        /** @var UserPolicy $policy */
        $policy = $this->app->make(UserPolicy::class);

        $result = $policy->enableTfa($user, $subject);

        $this->assertFalse($result);
    }

    public function test_successful_disable_tfa_action()
    {
        /** @var User $user */
        $user = User::factory([
            'password' => Hash::make('password'),
        ])->withPersonalTeam()->tfaEnabled()->create();

        /** @var UserPolicy $policy */
        $policy = $this->app->make(UserPolicy::class);

        $result = $policy->disableTfa($user, $user);

        $this->assertTrue($result);
    }

    public function test_disable_tfa_action_for_different_user()
    {
        /** @var User $subject */
        $subject = User::factory([
            'password' => Hash::make('password'),
        ])->withPersonalTeam()->tfaEnabled()->create();
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        /** @var UserPolicy $policy */
        $policy = $this->app->make(UserPolicy::class);

        $result = $policy->enableTfa($user, $subject);

        $this->assertFalse($result);
    }

    public function test_disable_tfa_action_for_user_with_no_tfa_enabled()
    {
        /** @var User $user */
        $user = User::factory([
            'password' => Hash::make('password'),
        ])->withPersonalTeam()->create();

        /** @var UserPolicy $policy */
        $policy = $this->app->make(UserPolicy::class);

        $result = $policy->disableTfa($user, $user);

        $this->assertFalse($result);
    }

    public function test_successful_logout_sessions_action()
    {
        /** @var User $user */
        $user = User::factory([
            'password' => Hash::make('password'),
        ])->withPersonalTeam()->create();

        /** @var UserPolicy $policy */
        $policy = $this->app->make(UserPolicy::class);

        $result = $policy->logoutOtherSessions($user, $user);

        $this->assertTrue($result);
    }

    public function test_logout_sessions_action_for_user_that_does_not_use_password()
    {
        /** @var User $user */
        $user = User::factory([
            'password' => null,
        ])->withPersonalTeam()->viaGithub()->create();

        /** @var UserPolicy $policy */
        $policy = $this->app->make(UserPolicy::class);

        $result = $policy->logoutOtherSessions($user, $user);

        $this->assertFalse($result);
    }

    public function test_logout_sessions_action_for_different_user()
    {
        /** @var User $subject */
        $subject = User::factory([
            'password' => Hash::make('password'),
        ])->withPersonalTeam()->create();
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        /** @var UserPolicy $policy */
        $policy = $this->app->make(UserPolicy::class);

        $result = $policy->logoutOtherSessions($user, $subject);

        $this->assertFalse($result);
    }

    public function test_successful_change_email_sessions_action()
    {
        /** @var User $user */
        $user = User::factory([
            'password' => Hash::make('password'),
        ])->withPersonalTeam()->create();

        /** @var UserPolicy $policy */
        $policy = $this->app->make(UserPolicy::class);

        $result = $policy->changeEmail($user, $user);

        $this->assertTrue($result);
    }

    public function test_change_email_action_for_user_that_does_not_use_password()
    {
        /** @var User $user */
        $user = User::factory([
            'password' => null,
        ])->withPersonalTeam()->viaGithub()->create();

        /** @var UserPolicy $policy */
        $policy = $this->app->make(UserPolicy::class);

        $result = $policy->changeEmail($user, $user);

        $this->assertFalse($result);
    }

    public function test_change_email_action_for_different_user()
    {
        /** @var User $subject */
        $subject = User::factory([
            'password' => Hash::make('password'),
        ])->withPersonalTeam()->create();
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        /** @var UserPolicy $policy */
        $policy = $this->app->make(UserPolicy::class);

        $result = $policy->changeEmail($user, $subject);

        $this->assertFalse($result);
    }

    public function test_successful_change_password_sessions_action()
    {
        /** @var User $user */
        $user = User::factory([
            'password' => Hash::make('password'),
        ])->withPersonalTeam()->create();

        /** @var UserPolicy $policy */
        $policy = $this->app->make(UserPolicy::class);

        $result = $policy->changePassword($user, $user);

        $this->assertTrue($result);
    }

    public function test_change_password_action_for_user_that_does_not_use_password()
    {
        /** @var User $user */
        $user = User::factory([
            'password' => null,
        ])->withPersonalTeam()->viaGithub()->create();

        /** @var UserPolicy $policy */
        $policy = $this->app->make(UserPolicy::class);

        $result = $policy->changePassword($user, $user);

        $this->assertFalse($result);
    }

    public function test_change_password_action_for_different_user()
    {
        /** @var User $subject */
        $subject = User::factory([
            'password' => Hash::make('password'),
        ])->withPersonalTeam()->create();
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        /** @var UserPolicy $policy */
        $policy = $this->app->make(UserPolicy::class);

        $result = $policy->changePassword($user, $subject);

        $this->assertFalse($result);
    }

    public function test_successful_delete_account_sessions_action()
    {
        /** @var User $user */
        $user = User::factory([
            'password' => Hash::make('password'),
        ])->withPersonalTeam()->create();

        /** @var UserPolicy $policy */
        $policy = $this->app->make(UserPolicy::class);

        $result = $policy->delete($user, $user);

        $this->assertTrue($result);
    }

    public function test_delete_account_action_for_different_user()
    {
        /** @var User $subject */
        $subject = User::factory([
            'password' => Hash::make('password'),
        ])->withPersonalTeam()->create();
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        /** @var UserPolicy $policy */
        $policy = $this->app->make(UserPolicy::class);

        $result = $policy->delete($user, $subject);

        $this->assertFalse($result);
    }
}
