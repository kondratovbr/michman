<?php

namespace Tests\Integration;

use Tests\AbstractIntegrationTest;

class IsDebugHelperTest extends AbstractIntegrationTest
{
    public function test_is_debug_helper_works_on_debug()
    {
        config(['app.debug' => true]);

        $this->assertEquals(true, isDebug());
    }

    public function test_is_debug_helper_works_on_not_debug()
    {
        config(['app.debug' => false]);

        $this->assertEquals(false, isDebug());
    }

    public function test_is_debug_helper_works_when_value_is_null()
    {
        config(['app.debug' => null]);

        $this->assertEquals(false, isDebug());
    }
}
