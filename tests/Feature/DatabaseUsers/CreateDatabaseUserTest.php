<?php

namespace Tests\Feature\DatabaseUsers;

use Mockery;
use Mockery\MockInterface;
use Tests\AbstractFeatureTest;

class CreateDatabaseUserTest extends AbstractFeatureTest
{
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(302);
    }
}
