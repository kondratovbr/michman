<?php

namespace Tests\Unit\Rules;

use App\Rules\UnixAnyPathRule;
use Tests\AbstractUnitTest;

class UnixAnyPathRuleTest extends AbstractUnitTest
{
    /** @dataProvider validPaths */
    public function test_valid_paths(string $value)
    {
        $rule = new UnixAnyPathRule;

        $result = $rule->passes('path', $value);

        $this->assertTrue($result);
    }

    /** @dataProvider invalidPaths */
    public function test_invalid_paths(mixed $value)
    {
        $rule = new UnixAnyPathRule;

        try {
            $result = $rule->passes('path', $value);
        } catch (\TypeError) {
            $result = false;
        }

        $this->assertFalse($result);
    }

    public function validPaths(): array
    {
        return [
            ['home'],
            ['root'],
            ['home/user/'],
            ['home/user///'],
            ['home/user/.ssh'],
            ['home/user/.ssh/'],
            ['home/user/foo/bar'],
            ['home///user'],
            ['home/фубар'],
            ['/home'],
            ['/root'],
            ['/home/user/'],
            ['/home/user///'],
            ['/home/user/.ssh'],
            ['/home/user/.ssh/'],
            ['/home/user/foo/bar'],
            ['/home///user'],
            ['/home/фубар'],
        ];
    }

    public function invalidPaths(): array
    {
        return [
            [''],
            [' '],
            [null],
            [true],
            [false],
            [123],
            [1.1],
            [[]],
            [['']],
            [['home/user']],

            // TODO: IMPORTANT! Figure out how to handle spaces in paths.
            ['home/u s e r/.ssh'],
        ];
    }
}
