<?php

namespace Tests\Integration;

use Tests\AbstractIntegrationTest;

class ClassesViewHelperTest extends AbstractIntegrationTest
{
    public function test_classes_view_helpers_works()
    {
        $result = classes(
            'foo',
            'bar',
            ['bar', 'baz'],
            ['foobar', ['foofoo', 'foobar', 'foobaz']],
        );

        $this->assertEquals('foo bar bar baz foobar foofoo foobar foobaz', $result);
    }
}
