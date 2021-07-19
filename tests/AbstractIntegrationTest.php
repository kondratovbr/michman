<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase;
use Tests\Traits\CreatesApplication;

abstract class AbstractIntegrationTest extends TestCase
{
    use CreatesApplication;
}
