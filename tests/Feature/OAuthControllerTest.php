<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VcsProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;
use Laravel\Socialite\Contracts\Provider as OAuthDriver;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;
use Laravel\Socialite\Contracts\User as OAuthUser;

// TODO: Forgot to test unlinking.

class OAuthControllerTest extends AbstractFeatureTest
{
    public function test_guest_can_be_redirected_to_github_oauth()
    {
        Socialite::shouldReceive('driver')
            ->once()
            ->with('github')
            ->andReturn(Mockery::mock(OAuthDriver::class, function (MockInterface $mock) {
                $mock->shouldReceive('scopes')
                    ->with(['user', 'repo', 'admin:public_key'])
                    ->once()
                    ->andReturnSelf();
                $mock->shouldReceive('redirectUrl')
                    ->with('http://localhost/oauth/github/callback/auth')
                    ->once()
                    ->andReturnSelf();
                $mock->shouldReceive('redirect')
                    ->withNoArgs()
                    ->once()
                    ->andReturn(new SymfonyRedirect('https://oauth.github.com/'));
            }));

        $response = $this->get('/oauth/github/auth');

        $response->assertRedirect('https://oauth.github.com/');
        $this->assertGuest();
    }

    public function test_authenticated_user_cannot_be_redirected_to_oauth()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        $response = $this->actingAs($user)->get('/oauth/github/auth');

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
    }

    public function test_guest_can_register_via_github_callback()
    {
        Socialite::shouldReceive('driver')
            ->once()
            ->with('github')
            ->andReturn(Mockery::mock(OAuthDriver::class, function (MockInterface $mock) {
                $mock->shouldReceive('redirectUrl')
                    ->with('http://localhost/oauth/github/callback/auth')
                    ->once()
                    ->andReturnSelf();
                $mock->shouldReceive('user')
                    ->zeroOrMoreTimes()
                    ->andReturn(Mockery::mock(OAuthUser::class, function (MockInterface $mock) {
                        $mock->shouldReceive('getId')
                            ->zeroOrMoreTimes()
                            ->andReturn('123456789');
                        $mock->shouldReceive('getEmail')
                            ->zeroOrMoreTimes()
                            ->andReturn('foo@bar.com');
                        $mock->shouldReceive('getNickname')
                            ->zeroOrMoreTimes()
                            ->andReturn('foobar');
                        $mock->token = 'foobarbaz';
                    }));
            }));

        Event::fake();

        $response = $this->get('/oauth/github/callback/auth?code=123456789&state=123456789');

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'foo@bar.com',
            'password' => null,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);

        /** @var User $user */
        $user = User::query()->firstWhere('email', 'foo@bar.com');

        $this->assertNull($user->emailVerifiedAt);
        $this->assertNotNull($user->getRememberToken());

        $oauthUser = $user->oauth('github');

        $this->assertNotNull($oauthUser);

        $this->assertEquals('github', $oauthUser->provider);
        $this->assertEquals('123456789', $oauthUser->oauthId);
        $this->assertEquals('foobar', $oauthUser->nickname);

        Event::assertDispatched(Registered::class);
    }

    public function test_vcs_provider_gets_created_when_user_registers_via_oauth()
    {
        Socialite::shouldReceive('driver')
            ->once()
            ->with('github')
            ->andReturn(Mockery::mock(OAuthDriver::class, function (MockInterface $mock) {
                $mock->shouldReceive('redirectUrl')
                    ->with('http://localhost/oauth/github/callback/auth')
                    ->once()
                    ->andReturnSelf();
                $mock->shouldReceive('user')
                    ->zeroOrMoreTimes()
                    ->andReturn(Mockery::mock(OAuthUser::class, function (MockInterface $mock) {
                        $mock->shouldReceive('getId')
                            ->zeroOrMoreTimes()
                            ->andReturn('123456789');
                        $mock->shouldReceive('getEmail')
                            ->zeroOrMoreTimes()
                            ->andReturn('foo@bar.com');
                        $mock->shouldReceive('getNickname')
                            ->zeroOrMoreTimes()
                            ->andReturn('foobar');
                        $mock->token = 'foobarbaz';
                    }));
            }));

        $response = $this->get('/oauth/github/callback/auth?code=123456789&state=123456789');

        $response->assertRedirect(route('home'));

        /** @var User $user */
        $user = User::query()->where('email', 'foo@bar.com')->firstOrFail();

        $this->assertDatabaseHas('vcs_providers', [
            'user_id' => $user->id,
            'provider' => 'github_v3',
            'external_id' => '123456789',
            'nickname' => 'foobar',
        ]);

        $this->assertCount(1, $user->vcsProviders);
        $this->assertNotNull($user->vcs('github_v3'));

        $vcsProvider = $user->vcs('github_v3');

        $this->assertEquals('foobarbaz', $vcsProvider->token->token);
    }

    public function test_previously_oauthed_user_can_login_via_oauth()
    {
        /** @var User $user */
        $user = User::factory()
            ->viaGithub([
                'oauth_id' => '123456789',
            ])
            ->withPersonalTeam()
            ->create();

        /** @var VcsProvider $vcsProvider */
        $vcsProvider = VcsProvider::factory([
            'provider' => 'github_v3',
            'external_id' => '123456789',
        ])
            ->for($user)
            ->create();

        Socialite::shouldReceive('driver')
            ->once()
            ->with('github')
            ->andReturn(Mockery::mock(OAuthDriver::class, function (MockInterface $mock) {
                $mock->shouldReceive('redirectUrl')
                    ->with('http://localhost/oauth/github/callback/auth')
                    ->once()
                    ->andReturnSelf();
                $mock->shouldReceive('user')
                    ->zeroOrMoreTimes()
                    ->andReturn(Mockery::mock(OAuthUser::class, function (MockInterface $mock) {
                        $mock->shouldReceive('getId')
                            ->zeroOrMoreTimes()
                            ->andReturn('123456789');
                        $mock->shouldReceive('getEmail')
                            ->zeroOrMoreTimes()
                            ->andReturn('foo@bar.com');
                        $mock->shouldReceive('getNickname')
                            ->zeroOrMoreTimes()
                            ->andReturn('foobar');
                        $mock->token = 'foobarbaz';
                    }));
            }));

        $response = $this->get('/oauth/github/callback/auth?code=123456789&state=123456789');

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('vcs_providers', [
            'id' => $vcsProvider->id,
            'user_id' => $user->id,
            'provider' => 'github_v3',
            'external_id' => '123456789',
            'nickname' => 'foobar',
        ]);

        $vcsProvider->refresh();

        $this->assertEquals('foobarbaz', $vcsProvider->token->token);
    }

    public function test_existing_vcs_provider_doesnt_get_updated_if_account_is_different()
    {
        /** @var User $user */
        $user = User::factory()
            ->viaGithub([
                'oauth_id' => '123456789',
            ])
            ->withPersonalTeam()
            ->create();

        /** @var VcsProvider $vcsProvider */
        $vcsProvider = VcsProvider::factory([
            'provider' => 'github_v3',
            'external_id' => '666',
        ])
            ->for($user)
            ->create();

        $token = $vcsProvider->token->token;

        Socialite::shouldReceive('driver')
            ->once()
            ->with('github')
            ->andReturn(Mockery::mock(OAuthDriver::class, function (MockInterface $mock) {
                $mock->shouldReceive('redirectUrl')
                    ->with('http://localhost/oauth/github/callback/auth')
                    ->once()
                    ->andReturnSelf();
                $mock->shouldReceive('user')
                    ->zeroOrMoreTimes()
                    ->andReturn(Mockery::mock(OAuthUser::class, function (MockInterface $mock) {
                        $mock->shouldReceive('getId')
                            ->zeroOrMoreTimes()
                            ->andReturn('123456789');
                        $mock->shouldReceive('getEmail')
                            ->zeroOrMoreTimes()
                            ->andReturn('foo@bar.com');
                        $mock->shouldReceive('getNickname')
                            ->zeroOrMoreTimes()
                            ->andReturn('foobar');
                        $mock->token = 'foobarbaz';
                    }));
            }));

        $response = $this->get('/oauth/github/callback/auth?code=123456789&state=123456789');

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('vcs_providers', [
            'id' => $vcsProvider->id,
            'user_id' => $user->id,
            'provider' => 'github_v3',
            'external_id' => '666',
            'nickname' => $vcsProvider->nickname,
        ]);

        $vcsProvider->refresh();

        $this->assertEquals($token, $vcsProvider->token->token);
    }

    public function test_existing_user_cannot_enable_oauth_by_logging_in()
    {
        /** @var User $user */
        $user = User::factory()
            ->withPersonalTeam()
            ->create([
                'email' => 'foo@bar.baz',
            ]);

        Socialite::shouldReceive('driver')
            ->with('github')
            ->once()
            ->andReturn(Mockery::mock(OAuthDriver::class, function (MockInterface $mock) {
                $mock->shouldReceive('redirectUrl')
                    ->with('http://localhost/oauth/github/callback/auth')
                    ->once()
                    ->andReturnSelf();
                $mock->shouldReceive('user')
                    ->once()
                    ->andReturn(Mockery::mock(OAuthUser::class, function (MockInterface $mock) {
                        $mock->shouldReceive('getId')
                            ->zeroOrMoreTimes()
                            ->andReturn('12345');
                        $mock->shouldReceive('getEmail')
                            ->zeroOrMoreTimes()
                            ->andReturn('foo@bar.baz');
                        $mock->shouldReceive('getNickname')
                            ->withNoArgs()
                            ->zeroOrMoreTimes()
                            ->andReturn('theuser');
                        $mock->token = '987654321';
                    }));
            }));

        $response = $this->get('/oauth/github/callback/auth?code=123456789&state=123456789');

        $response->assertRedirect(route('login'));

        $this->assertGuest();

        $user->refresh();

        $this->assertNotNull($user->password);

        $this->assertEquals(0, $user->oauthUsers()->count());
        $this->assertEquals(0, $user->vcsProviders()->count());
    }

    public function test_authed_user_can_be_redirected_to_oauth_for_linking()
    {
        /** @var User $user */
        $user = User::factory()
            ->viaGithub([
                'oauth_id' => '123456789',
            ])
            ->withPersonalTeam()
            ->create();

        Socialite::shouldReceive('driver')
            ->once()
            ->with('gitlab')
            ->andReturn(Mockery::mock(OAuthDriver::class, function (MockInterface $mock) {
                $mock->shouldReceive('redirectUrl')
                    ->with('http://localhost/oauth/gitlab/callback/link')
                    ->once()
                    ->andReturnSelf();
                $mock->shouldReceive('redirect')
                    ->withNoArgs()
                    ->once()
                    ->andReturn(new SymfonyRedirect('https://gitlab.com/oauth'));
            }));

        $response = $this->actingAs($user)->get('/oauth/gitlab/link');

        $response->assertRedirect('https://gitlab.com/oauth');
        $this->assertAuthenticatedAs($user);
    }

    public function test_guest_cannot_be_redirected_to_oauth_for_linking()
    {
        $response = $this->get('/oauth/gitlab/link');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_authed_user_can_link_new_oauth_account()
    {
        /** @var User $user */
        $user = User::factory()
            ->viaGithub([
                'oauth_id' => '123456789',
            ])
            ->withPersonalTeam()
            ->create();

        /** @var VcsProvider $vcsProvider */
        $vcsProvider = VcsProvider::factory([
            'provider' => 'github_v3',
            'external_id' => '123456789',
        ])
            ->for($user)
            ->create();

        Socialite::shouldReceive('driver')
            ->once()
            ->with('gitlab')
            ->andReturn(Mockery::mock(OAuthDriver::class, function (MockInterface $mock) {
                $mock->shouldReceive('redirectUrl')
                    ->with('http://localhost/oauth/gitlab/callback/link')
                    ->once()
                    ->andReturnSelf();
                $mock->shouldReceive('user')
                    ->zeroOrMoreTimes()
                    ->andReturn(Mockery::mock(OAuthUser::class, function (MockInterface $mock) {
                        $mock->shouldReceive('getId')
                            ->zeroOrMoreTimes()
                            ->andReturn('66666');
                        $mock->shouldReceive('getEmail')
                            ->zeroOrMoreTimes()
                            ->andReturn('foo@bar.biz');
                        $mock->shouldReceive('getNickname')
                            ->zeroOrMoreTimes()
                            ->andReturn('foofoo');
                        $mock->token = 'foofoofoo';
                    }));
            }));

        $response = $this->actingAs($user)->get('/oauth/gitlab/callback/link?code=123456789&state=123456789');

        $response->assertRedirect(route('account.show', 'profile'));

        $this->assertEquals(2, $user->oauthUsers()->count());

        $oauth = $user->oauth('gitlab');

        $this->assertEquals('gitlab', $oauth->provider);
        $this->assertEquals('66666', $oauth->oauthId);
        $this->assertEquals('foofoo', $oauth->nickname);
    }

    public function test_user_cannot_link_the_same_oauth_provider()
    {
        /** @var User $user */
        $user = User::factory()
            ->viaGithub([
                'oauth_id' => '123456789',
            ])
            ->withPersonalTeam()
            ->create();

        /** @var VcsProvider $vcsProvider */
        $vcsProvider = VcsProvider::factory([
            'provider' => 'github_v3',
            'external_id' => '123456789',
        ])
            ->for($user)
            ->create();

        Socialite::shouldReceive('driver')->never();

        $response = $this->actingAs($user)->get('/oauth/github/callback/link?code=123456789&state=123456789');

        $response->assertStatus(500);

        $this->assertEquals(1, $user->oauthUsers()->count());

        $oauth = $user->oauth('github');

        $this->assertEquals('github', $oauth->provider);
        $this->assertEquals('123456789', $oauth->oauthId);
    }
}
