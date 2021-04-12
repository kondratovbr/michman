<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Foundation\Testing\WithFaker;

class AbstractFeatureTest extends TestCase
{
    use CreatesApplication, RefreshDatabase, WithFaker;
}
