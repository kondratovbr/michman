<?php

namespace Tests\Feature;

use App\Models\User;
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
                $mock->shouldReceive('redirect')
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
            'provider' => 'github',
            'external_id' => '123456789',
            'nickname' => 'foobar',
            'key' => null,
            'secret' => null,
        ]);

        $this->assertCount(1, $user->vcsProviders);
        $this->assertNotNull($user->vcs('github'));

        $vcsProvider = $user->vcs('github');

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

        Socialite::shouldReceive('driver')
            ->with('github')
            ->once()
            ->andReturn(Mockery::mock(OAuthDriver::class, function (MockInterface $mock) {
                $mock->shouldReceive('user')
                    ->once()
                    ->andReturn(Mockery::mock(OAuthUser::class, function (MockInterface $mock) {
                        $mock->shouldReceive('getId')
                            ->once()
                            ->andReturn('12345');
                    }));
            }));

        $response = $this->get('/oauth/github/callback?code=123456789&state=123456789');

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
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
                            ->twice()
                            ->andReturn('12345');
                        $mock->shouldReceive('getEmail')
                            ->once()
                            ->andReturn('foo@bar.baz');
                    }));
            }));

        $response = $this->get('/oauth/github/callback?code=123456789&state=123456789');

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);

        $user->refresh();

        $this->assertEquals('github', $user->oauthProvider);
        $this->assertEquals('12345', $user->oauthId);
        $this->assertNotNull($user->password);
    }
}
