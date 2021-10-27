<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VcsProvider;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;
use Laravel\Socialite\Contracts\Provider as OAuthDriver;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;
use Laravel\Socialite\Contracts\User as OAuthUser;

class OAuthTest extends AbstractFeatureTest
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
                $mock->shouldReceive('redirect')
                    ->withNoArgs()
                    ->once()
                    ->andReturn(new SymfonyRedirect('https://oauth.github.com/'));
            }));

        $response = $this->get('/oauth/github/login');

        $response->assertRedirect('https://oauth.github.com/');
        $this->assertGuest();
    }

    public function test_authenticated_user_cannot_be_redirected_to_oauth()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        $response = $this->actingAs($user)->get('/oauth/github/login');

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
    }

    public function test_guest_can_register_via_github_callback()
    {
        Socialite::shouldReceive('driver')
            ->once()
            ->with('github')
            ->andReturn(Mockery::mock(OAuthDriver::class, function (MockInterface $mock) {
                $mock->shouldReceive('user')
                    ->once()
                    ->andReturn(Mockery::mock(OAuthUser::class, function (MockInterface $mock) {
                        $mock->shouldReceive('getId')
                            ->times(3)
                            ->andReturn('123456789');
                        $mock->shouldReceive('getEmail')
                            ->twice()
                            ->andReturn('foo@bar.com');
                        $mock->shouldReceive('getNickname')
                            ->once()
                            ->andReturn('foobar');
                        $mock->token = 'foobarbaz';
                    }));
            }));

        $response = $this->get('/oauth/github/callback?code=123456789&state=123456789');

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'foo@bar.com',
            'password' => null,
            'oauth_provider' => 'github',
            'oauth_id' => '123456789',
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);

        /** @var User $user */
        $user = User::query()->firstWhere('email', 'foo@bar.com');

        $this->assertNotNull($user->emailVerifiedAt);
        $this->assertNotNull($user->getRememberToken());
    }

    public function test_vcs_provider_gets_created_when_user_registers_via_oauth()
    {
        Socialite::shouldReceive('driver')
            ->once()
            ->with('github')
            ->andReturn(Mockery::mock(OAuthDriver::class, function (MockInterface $mock) {
                $mock->shouldReceive('user')
                    ->once()
                    ->andReturn(Mockery::mock(OAuthUser::class, function (MockInterface $mock) {
                        $mock->shouldReceive('getId')
                            ->times(3)
                            ->andReturn('123456789');
                        $mock->shouldReceive('getEmail')
                            ->twice()
                            ->andReturn('foo@bar.com');
                        $mock->shouldReceive('getNickname')
                            ->once()
                            ->andReturn('foobar');
                        $mock->token = 'foobarbaz';
                    }));
            }));

        $response = $this->get('/oauth/github/callback?code=123456789&state=123456789');

        $response->assertRedirect(route('home'));

        /** @var User $user */
        $user = User::query()->where('email', 'foo@bar.com')->firstOrFail();

        $this->assertDatabaseHas('vcs_providers', [
            'user_id' => $user->id,
            'provider' => 'github_v3',
            'external_id' => '123456789',
            'nickname' => 'foobar',
            'key' => null,
            'secret' => null,
        ]);

        $this->assertCount(1, $user->vcsProviders);
        $this->assertNotNull($user->vcs('github_v3'));

        $vcsProvider = $user->vcs('github_v3');

        $this->assertEquals('foobarbaz', $vcsProvider->token);
    }

    public function test_previously_oauthed_user_can_login_via_oauth()
    {
        /** @var User $user */
        $user = User::factory()
            ->viaGithub()
            ->withPersonalTeam()
            ->create([
                'oauth_id' => '12345',
            ]);

        /** @var VcsProvider $vcsProvider */
        $vcsProvider = VcsProvider::factory([
            'provider' => 'github_v3',
        ])
            ->for($user)
            ->create();

        Socialite::shouldReceive('driver')
            ->with('github')
            ->once()
            ->andReturn(Mockery::mock(OAuthDriver::class, function (MockInterface $mock) use ($user, $vcsProvider) {
                $mock->shouldReceive('user')
                    ->once()
                    ->andReturn(Mockery::mock(OAuthUser::class, function (MockInterface $mock) use ($user, $vcsProvider) {
                        $mock->shouldReceive('getId')
                            ->withNoArgs()
                            ->times(3)
                            ->andReturn($vcsProvider->externalId);
                        $mock->shouldReceive('getNickname')
                            ->withNoArgs()
                            ->once()
                            ->andReturn($vcsProvider->nickname);
                        $mock->shouldReceive('getEmail')
                            ->withNoArgs()
                            ->once()
                            ->andReturn($user->email);
                        $mock->token = '987654321';
                    }));
            }));

        $response = $this->get('/oauth/github/callback?code=123456789&state=123456789');

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('vcs_providers', [
            'id' => $vcsProvider->id,
            'user_id' => $user->id,
            'provider' => 'github_v3',
            'external_id' => $vcsProvider->externalId,
            'nickname' => $vcsProvider->nickname,
            'key' => null,
            'secret' => null,
        ]);

        $vcsProvider->refresh();

        $this->assertEquals('987654321', $vcsProvider->token);
        $this->assertNull($vcsProvider->key);
        $this->assertNull($vcsProvider->secret);
    }

    public function test_existing_user_can_enable_oauth_by_logging_in()
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
                $mock->shouldReceive('user')
                    ->once()
                    ->andReturn(Mockery::mock(OAuthUser::class, function (MockInterface $mock) {
                        $mock->shouldReceive('getId')
                            ->times(3)
                            ->andReturn('12345');
                        $mock->shouldReceive('getEmail')
                            ->once()
                            ->andReturn('foo@bar.baz');
                        $mock->shouldReceive('getNickname')
                            ->withNoArgs()
                            ->once()
                            ->andReturn('theuser');
                        $mock->token = '987654321';
                    }));
            }));

        $response = $this->get('/oauth/github/callback?code=123456789&state=123456789');

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);

        $user->refresh();

        $this->assertEquals('github', $user->oauthProvider);
        $this->assertEquals('12345', $user->oauthId);
        $this->assertNotNull($user->password);

        $this->assertDatabaseHas('vcs_providers', [
            'user_id' => $user->id,
            'provider' => 'github_v3',
            'external_id' =>'12345',
            'nickname' => 'theuser',
            'key' => null,
            'secret' => null,
        ]);

        /** @var VcsProvider $vcsProvider */
        $vcsProvider = $user->vcsProviders()->first();

        $this->assertEquals('987654321', $vcsProvider->token);
        $this->assertNull($vcsProvider->key);
        $this->assertNull($vcsProvider->secret);
    }

    public function test_multiple_vcs_providers_can_be_created()
    {
        /** @var User $user */
        $user = User::factory()
            ->viaGithub()
            ->withPersonalTeam()
            ->create([
                'oauth_id' => '12345',
            ]);

        /** @var VcsProvider $vcsProvider */
        $vcsProvider = VcsProvider::factory([
            'provider' => 'github_v3',
            'external_id' => '123',
            'nickname' => 'Jekyll',
        ])
            ->for($user)
            ->create();

        Socialite::shouldReceive('driver')
            ->with('gitlab')
            ->once()
            ->andReturn(Mockery::mock(OAuthDriver::class, function (MockInterface $mock) use ($user, $vcsProvider) {
                $mock->shouldReceive('user')
                    ->once()
                    ->andReturn(Mockery::mock(OAuthUser::class, function (MockInterface $mock) use ($user, $vcsProvider) {
                        $mock->shouldReceive('getId')
                            ->withNoArgs()
                            ->times(3)
                            ->andReturn('321');
                        $mock->shouldReceive('getNickname')
                            ->withNoArgs()
                            ->once()
                            ->andReturn('Hyde');
                        $mock->shouldReceive('getEmail')
                            ->withNoArgs()
                            ->once()
                            ->andReturn($user->email);
                        $mock->token = '666666';
                    }));
            }));

        $response = $this->get('/oauth/gitlab/callback?code=123456789&state=123456789');

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);

        $this->assertDatabaseHas('vcs_providers', [
            'id' => $vcsProvider->id,
            'user_id' => $user->id,
            'provider' => 'github_v3',
            'external_id' => $vcsProvider->externalId,
            'nickname' => $vcsProvider->nickname,
            'key' => null,
            'secret' => null,
        ]);

        $this->assertDatabaseHas('vcs_providers', [
            'user_id' => $user->id,
            'provider' => 'gitlab_v4',
            'external_id' => '321',
            'nickname' => 'Hyde',
            'key' => null,
            'secret' => null,
        ]);

        $token = $vcsProvider->token;

        $vcsProvider->refresh();

        $this->assertEquals($token, $vcsProvider->token);
        $this->assertNull($vcsProvider->key);
        $this->assertNull($vcsProvider->secret);

        /** @var VcsProvider $vcsProviderTwo */
        $vcsProviderTwo = $user->vcsProviders()->firstWhere('provider', 'gitlab_v4');

        $this->assertNotNull($vcsProviderTwo);

        $this->assertEquals('666666', $vcsProviderTwo->token);
        $this->assertNull($vcsProviderTwo->key);
        $this->assertNull($vcsProviderTwo->secret);
    }
}
