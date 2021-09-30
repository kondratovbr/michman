<?php

namespace Tests\Unit\Rules;

use App\Rules\DomainRule;
use Tests\AbstractUnitTest;

class DomainRuleTest extends AbstractUnitTest
{
    /** @dataProvider validDomains */
    public function test_valid_domains(string $value)
    {
        $rule = new DomainRule;

        $result = $rule->passes('domain', $value);

        $this->assertTrue($result);
    }

    /** @dataProvider invalidDomains */
    public function test_invalid_domains(mixed $value)
    {
        $rule = new DomainRule;

        try {
            $result = $rule->passes('domain', $value);
        } catch (\TypeError) {
            $result = false;
        }

        $this->assertFalse($result);
    }

    public function validDomains(): array
    {
        return [
            ['foo.com'],
            ['www.foo.com'],
            ['bar.com'],
            ['foo.bar.baz'],
            ['f_o_o.cc'],
            ['123.com'],
            ['domain.comdomain.com'],
        ];
    }

    public function invalidDomains(): array
    {
        return [
            ['f f f'],
            ['фубар'],
            ['nocom'],
            ['domain1.com, domain2.com'],
            ['domain1.com domain2.com'],
            [null],
            [123],
            [1.1],
            [true],
            [false],
            [[]],
            [['foo.com']],
            [new class {}],
        ];
    }
}
