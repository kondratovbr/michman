<?php

namespace Tests\Unit;

use App\DataTransferObjects\VcsProviderDto;
use Laravel\Socialite\Two\User;
use Tests\AbstractUnitTest;

class VcsProviderDtoTest extends AbstractUnitTest
{
    public function test_github()
    {
        $oauthUser = new User();

        $data = [
            'id' => '123',
            'nickname' => 'FreeGuy',
            'name' => 'The Free Guy',
            'email' => 'guy@free.com',
            'avatar' => 'https://free.com/avatars/guy.jpg',
            'token' => '1234567890',
            'refreshToken' => '9876',
            'expiresIn' => '600',
        ];

        $oauthUser->map($data)->setRaw($data);

        $dto = VcsProviderDto::fromOauth($oauthUser, 'github_v3');

        $this->assertEquals('github_v3', $dto->provider);
        $this->assertEquals('123', $dto->external_id);
        $this->assertEquals('FreeGuy', $dto->nickname);
        $this->assertEquals('1234567890', $dto->token);
        $this->assertNull($dto->key);
        $this->assertNull($dto->secret);
    }

    public function test_gitlab()
    {
        $oauthUser = new User();

        $data = [
            'id' => '123',
            'nickname' => 'FreeGuy',
            'name' => 'The Free Guy',
            'email' => 'guy@free.com',
            'avatar' => 'https://free.com/avatars/guy.jpg',
            'token' => '1234567890',
            'refreshToken' => '9876',
            'expiresIn' => '600',
        ];

        $oauthUser->map($data)->setRaw($data);

        $dto = VcsProviderDto::fromOauth($oauthUser, 'gitlab');

        $this->assertEquals('gitlab', $dto->provider);
        $this->assertEquals('123', $dto->external_id);
        $this->assertEquals('FreeGuy', $dto->nickname);
        $this->assertEquals('1234567890', $dto->token);
        $this->assertNull($dto->key);
        $this->assertNull($dto->secret);
    }

    public function test_bitbucket()
    {
        $oauthUser = new User();

        $data = [
            'id' => '{123}',
            'nickname' => 'FreeGuy',
            'name' => 'The Free Guy',
            'email' => 'guy@free.com',
            'avatar' => 'https://free.com/avatars/guy.jpg',
            'token' => '1234567890',
            'refreshToken' => '9876',
            'expiresIn' => '600',
        ];

        $oauthUser->map($data)->setRaw($data);

        $dto = VcsProviderDto::fromOauth($oauthUser, 'bitbucket');

        $this->assertEquals('bitbucket', $dto->provider);
        $this->assertEquals('{123}', $dto->external_id);
        $this->assertEquals('FreeGuy', $dto->nickname);
        $this->assertEquals('1234567890', $dto->token);
        $this->assertNull($dto->key);
        $this->assertNull($dto->secret);
    }
}
