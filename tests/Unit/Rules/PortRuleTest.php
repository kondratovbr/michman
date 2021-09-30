<?php

namespace Tests\Unit\Rules;

use App\Rules\PortRule;
use Tests\AbstractUnitTest;

class PortRuleTest extends AbstractUnitTest
{
    /** @dataProvider validPorts */
    public function test_valid_ports(string|int $value)
    {
        $rule = new PortRule;

        $result = $rule->passes('port', $value);

        $this->assertTrue($result);
    }

    /** @dataProvider invalidPorts */
    public function test_invalid_ports(mixed $value)
    {
        $rule = new PortRule;

        try {
            $result = $rule->passes('port', $value);
        } catch (\TypeError) {
            $result = false;
        }

        $this->assertFalse($result);
    }

    /** @dataProvider validRanges */
    public function test_valid_ranges(string $value)
    {
        $rule = new PortRule;

        $result = $rule->passes('port', $value);

        $this->assertTrue($result);
    }

    public function validPorts(): array
    {
        return [
            [0],
            [1],
            [666],
            [65535],
            ['0'],
            ['1'],
            ['666'],
            ['65535'],
        ];
    }

    public function invalidPorts(): array
    {
        return [
            [null],
            [-1],
            [-666],
            [65536],
            [100500],
            [1.1],
            ['-1'],
            ['-666'],
            ['65536'],
            ['100500'],
            ['foobar'],
            ['1 2 3'],
            ['foo_bar'],
            ['1/15'],
            [['']],
            [['foo', 'bar']],
            [new class {}],
        ];
    }

    public function validRanges(): array
    {
        return [
            ['1:2'],
            ['1:65535'],
            ['666:667'],
            ['666:666'],
            ['1:1'],
        ];
    }
}
