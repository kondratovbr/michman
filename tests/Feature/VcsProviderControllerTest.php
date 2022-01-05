<?php

namespace Tests\Feature;

use App\Events\Users\FlashMessageEvent;
use App\Models\User;
use App\Models\VcsProvider;
use Illuminate\Support\Facades\Event;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;
use Laravel\Socialite\Contracts\Provider as OAuthDriver;
use Laravel\Socialite\Contracts\User as OAuthUser;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;

// TODO: Forgot to test unlinking.

class VcsProviderControllerTest extends AbstractFeatureTest
{
    public function test_github_redirect()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        Socialite::shouldReceive('driver')
            ->once()
            ->with('github')
            ->andReturn(Mockery::mock(OAuthDriver::class, function (MockInterface $mock) {
                $mock->shouldReceive('scopes')
                    ->with(['user', 'repo', 'admin:public_key'])
                    ->once()
                    ->andReturnSelf();
                $mock->shouldReceive('redirectUrl')
                    ->with('http://localhost/oauth/github/callback/vcs')
                    ->once()
                    ->andReturnSelf();
                $mock->shouldReceive('redirect')
                    ->once()
                    ->andReturn(new SymfonyRedirect('https://oauth.github.com/'));
            }));

        $response = $this->actingAs($user)->get('/vcs/github/link');

        $response->assertRedirect('https://oauth.github.com/');
    }

    public function test_github_redirect_for_guest()
    {
        Socialite::shouldReceive('driver')->never();

        $response = $this->get('/vcs/github/link');

        $response->assertRedirect(route('login'));
    }

    public function test_github_link()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        Socialite::shouldReceive('driver')
            ->once()
            ->with('github')
            ->andReturn(Mockery::mock(OAuthDriver::class, function (MockInterface $mock) {
                $mock->shouldReceive('redirectUrl')
                    ->with('http://localhost/oauth/github/callback/vcs')
                    ->once()
                    ->andReturnSelf();
                $mock->shouldReceive('user')
                    ->once()
                    ->andReturn(Mockery::mock(OAuthUser::class, function (MockInterface $mock) {
                        $mock->shouldReceive('getId')
                            ->twice()
                            ->andReturn('123456789');
                        $mock->shouldReceive('getNickname')
                            ->once()
                            ->andReturn('theuser');
                        $mock->token = 'foobar';
                    }));
            }));

        Event::fake();

        $response = $this->actingAs($user)->get('/oauth/github/callback/vcs?code=123456789&state=123456789');

        $response->assertRedirect(route('account.show', 'vcs'));
        $this->assertDatabaseHas('vcs_providers', [
            'user_id' => $user->id,
            'provider' => 'github_v3',
            'external_id' => '123456789',
            'nickname' => 'theuser',
        ]);
        $this->assertCount(1, $user->vcsProviders);
        $this->assertNotNull($user->vcs('github_v3'));
        $this->assertEquals('foobar', $user->vcs('github_v3')->token->token);

        Event::assertDispatched(FlashMessageEvent::class);
    }

    public function test_github_refresh()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        /** @var VcsProvider $vcsProvider */
        $vcsProvider = VcsProvider::factory()->for($user)->create([
            'provider' => 'github_v3',
            'external_id' => '123456789',
        ]);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('github')
            ->andReturn(Mockery::mock(OAuthDriver::class, function (MockInterface $mock) {
                $mock->shouldReceive('redirectUrl')
                    ->with('http://localhost/oauth/github/callback/vcs')
                    ->once()
                    ->andReturnSelf();
                $mock->shouldReceive('user')
                    ->once()
                    ->andReturn(Mockery::mock(OAuthUser::class, function (MockInterface $mock) {
                        $mock->shouldReceive('getId')
                            ->twice()
                            ->andReturn('123456789');
                        $mock->shouldReceive('getNickname')
                            ->once()
                            ->andReturn('theuser');
                        $mock->token = 'foobar';
                    }));
            }));

        Event::fake();

        $response = $this->actingAs($user)->get('/oauth/github/callback/vcs?code=123456789&state=123456789');

        $response->assertRedirect(route('account.show', 'vcs'));
        $this->assertDatabaseHas('vcs_providers', [
            'user_id' => $user->id,
            'provider' => 'github_v3',
            'external_id' => '123456789',
            'nickname' => 'theuser',
        ]);
        $this->assertCount(1, $user->vcsProviders);
        $this->assertNotNull($user->vcs('github_v3'));
        $this->assertEquals('foobar', $user->vcs('github_v3')->token->token);

        Event::assertDispatched(FlashMessageEvent::class);
    }
}
