<?php

namespace Tests\Unit\Rules;

use App\Rules\UnixAbsolutePathRule;
use App\Support\Arr;
use Tests\AbstractUnitTest;

class UnixAbsolutePathRuleTest extends AbstractUnitTest
{
    /** @dataProvider validPaths */
    public function test_valid_paths(string $value)
    {
        $rule = new UnixAbsolutePathRule;

        $result = $rule->passes('path', $value);

        $this->assertTrue($result);
    }

    /** @dataProvider invalidPaths */
    public function test_invalid_paths(mixed $value)
    {
        $rule = new UnixAbsolutePathRule;

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
            ['/home'],
            ['/root'],
            ['/'],
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
        $values = Arr::map($this->validPaths(), fn(array $case) => [substr($case[0], 1)]);

        return Arr::merge($values, [
            [''],
            [' '],
            [null],
            [true],
            [false],
            [123],
            [1.1],
            [[]],
            [['']],
            [['/home/user']],

            // TODO: IMPORTANT! Figure out how to handle spaces in paths.
            ['/home/u s e r/.ssh'],
        ]);
    }
}
