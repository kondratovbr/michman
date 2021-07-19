<?php

namespace Tests\Integration;

use App\Models\User;
use Tests\AbstractIntegrationTest;

class UserViewHelperTest extends AbstractIntegrationTest
{
    public function test_user_view_helper_returns_authenticated_user()
    {
        $this->actingAs($user = User::factory()->withPersonalTeam()->create());

        $this->assertEquals($user, user());
    }
}
