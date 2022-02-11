<?php

namespace Tests\Unit\Rules;

use App\Rules\SshPublicKeyRule;
use Tests\AbstractUnitTest;
use Throwable;

class SshPublicKeyRuleTest extends AbstractUnitTest
{
    /** @dataProvider validKeys */
    public function test_valid_keys(string $value)
    {
        $rule = new SshPublicKeyRule;

        $result = $rule->passes('key', $value);

        $this->assertTrue($result);
    }

    /** @dataProvider invalidKeys */
    public function test_invalid_keys(mixed $value)
    {
        $rule = new SshPublicKeyRule;

        try {
            $result = $rule->passes('key', $value);
        } catch (Throwable) {
            $result = false;
        }

        $this->assertFalse($result);
    }

    public function validKeys(): array
    {
        return [
            ['ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIMjxqKfNv/v5/xZsbbn3LrJo+n9VR8QvwWXnYaKnjGJ8 klocko.com - deploy key'],
            ['ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIMjxqKfNv/v5/xZsbbn3LrJo+n9VR8QvwWXnYaKnjGJ8 фубар'],
            ['ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIMjxqKfNv/v5/xZsbbn3LrJo+n9VR8QvwWXnYaKnjGJ8'],
            ['ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIMjxqKfNv/v5/xZsbbn3LrJo+n9VR8QvwWXnYaKnjGJ8   '],
            ['ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIMjxqKfNv/v5/xZsbbn3LrJo+n9VR8QvwWXnYaKnjGJ8 klocko'],
            ['ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIMjxqKfNv/v5/xZsbbn3LrJo+n9VR8QvwWXnYaKnjGJ8 klocko.com'],
        ];
    }

    public function invalidKeys(): array
    {
        return [
            ['ssh-ed25510 AAAAC3NzaC1lZDI1NTE5AAAAIMjxqKfNv/v5/xZsbbn3LrJo+n9VR8QvwWXnYaKnjGJ8 klocko.com - deploy key'],
            ['ssh-ed25519 6666C3NzaC1lZDI1NTE5AAAAIMjxqKfNv/v5/xZsbbn3LrJo+n9VR8QvwWXnYaKnjGJ8'],
            ['ssh-rsa AAAAC3NzaC1lZDI1NTE5AAAAIMjxqKfNv/v5/xZsbbn3LrJo+n9VR8QvwWXnYaKnjGJ9'],
            ['   ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIMjxqKfNv/v5/xZsbbn3LrJo+n9VR8QvwWXnYaKnjGJ8'],
            ['ssh-ed25519AAAAC3NzaC1lZDI1NTE5AAAAIMjxqKfNv/v5/xZsbbn3LrJo+n9VR8QvwWXnYaKnjGJ8'],
            [''],
            ['foobar'],
            [null],
            [123],
            [1.1],
            [new class {}],
            [true],
            [false],
            [-1],
            [['ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIMjxqKfNv/v5/xZsbbn3LrJo+n9VR8QvwWXnYaKnjGJ8']],
            [[]],
            [['']],
            [new class {}],

            // TODO: IMPORTANT! Improve the rule so this situation (multiple spaces) gets cleaned and accepted. Handle other space-like separator characters as well.
            ['ssh-ed25519   AAAAC3NzaC1lZDI1NTE5AAAAIMjxqKfNv/v5/xZsbbn3LrJo+n9VR8QvwWXnYaKnjGJ8'],
        ];
    }
}
