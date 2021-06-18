<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VcsProvider;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;
use Laravel\Socialite\Contracts\Provider as OAuthDriver;
use Laravel\Socialite\Contracts\User as OAuthUser;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;

class VcsProviderControllerTest extends AbstractFeatureTest
{
    public function test_github_redirect()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        Socialite::shouldReceive('driver')
            ->once()
            ->with('github')
            ->andReturn(Mockery::mock(OAuthDriver::class, function (MockInterface $mock) {
                $mock->shouldReceive('scopes')
                    ->with(['repo', 'admin:public_key'])
                    ->once()
                    ->andReturnSelf();
                $mock->shouldReceive('with')
                    ->with(['redirect_uri' => 'http://localhost/oauth/github/vcs-callback'])
                    ->once()
                    ->andReturnSelf();
                $mock->shouldReceive('redirect')
                    ->once()
                    ->andReturn(new SymfonyRedirect('https://oauth.github.com/'));
            }));

        $response = $this->get('/vcs/link/github');

        $response->assertRedirect();
    }

    public function test_github_link()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        Socialite::shouldReceive('driver')
            ->once()
            ->with('github')
            ->andReturn(Mockery::mock(OAuthDriver::class, function (MockInterface $mock) {
                $mock->shouldReceive('user')
                    ->once()
                    ->andReturn(Mockery::mock(OAuthUser::class, function (MockInterface $mock) {
                        $mock->shouldReceive('getId')
                            ->once()
                            ->andReturn('123456789');
                        $mock->shouldReceive('getNickname')
                            ->once()
                            ->andReturn('theuser');
                        $mock->token = 'foobar';
                    }));
            }));

        $response = $this->actingAs($user)->get('/oauth/github/vcs-callback?code=123456789&state=123456789');

        $response->assertRedirect();
        $this->assertDatabaseHas('vcs_providers', [
            'user_id' => $user->id,
            'provider' => 'github',
            'external_id' => '123456789',
            'nickname' => 'theuser',
            'key' => null,
            'secret' => null,
        ]);
        $this->assertCount(1, $user->vcsProviders);
        $this->assertNotNull($user->vcs('github'));
        $this->assertEquals('foobar', $user->vcs('github')->token);
    }

    public function test_github_refresh()
    {
        /** @var User $user */
        $user = User::factory()->withPersonalTeam()->create();

        /** @var VcsProvider $vcsProvider */
        $vcsProvider = VcsProvider::factory()->for($user)->create([
            'provider' => 'github',
            'external_id' => '123456789',
        ]);

        Socialite::shouldReceive('driver')
            ->once()
            ->with('github')
            ->andReturn(Mockery::mock(OAuthDriver::class, function (MockInterface $mock) {
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

        $response = $this->actingAs($user)->get('/oauth/github/vcs-callback?code=123456789&state=123456789');

        $user->refresh();
        $vcsProvider->refresh();

        $response->assertRedirect();
        $this->assertDatabaseHas('vcs_providers', [
            'user_id' => $user->id,
            'provider' => 'github',
            'external_id' => '123456789',
            'key' => null,
            'secret' => null,
        ]);
        $this->assertCount(1, $user->vcsProviders);
        $this->assertNotNull($user->vcs('github'));
        $this->assertEquals('foobar', $user->vcs('github')->token);
    }
}
