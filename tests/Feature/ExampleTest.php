<?php

namespace Tests\Feature;

use Tests\AbstractFeatureTest;

class ExampleTest extends AbstractFeatureTest
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertStatus(302);
    }
}
