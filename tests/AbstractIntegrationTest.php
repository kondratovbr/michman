<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Traits\CreatesApplication;

abstract class AbstractIntegrationTest extends TestCase
{
    use CreatesApplication, RefreshDatabase, WithFaker;
}
