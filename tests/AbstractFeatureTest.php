<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;

class AbstractFeatureTest extends TestCase
{
    use CreatesApplication, RefreshDatabase, WithFaker;

    /**
     * Declare a new mocked service using Mockery and app->bind() method.
     * This way we can actually bind services that require constructor parameters,
     * which can't be mocked by app->instance() due to how Laravel DI works.
     */
    protected function mockBind(string $abstract, ...$arguments): void
    {
        $this->app->bind($abstract, fn() => Mockery::mock($abstract, ...$arguments));
    }
}
