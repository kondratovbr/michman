<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

class AbstractFeatureTest extends TestCase
{
    use CreatesApplication, RefreshDatabase;
}
