<?php

namespace Tests\Unit\Rules;

use App\Rules\GitRepoNameRule;
use Tests\AbstractUnitTest;

class GitRepoNameRuleTest extends AbstractUnitTest
{
    /** @dataProvider validRepoNames */
    public function test_valid_repo_names(string $value)
    {
        $rule = new GitRepoNameRule;

        $result = $rule->passes('repo', $value);

        $this->assertTrue($result);
    }

    /** @dataProvider invalidRepoNames */
    public function test_invalid_repo_names(mixed $value)
    {
        $rule = new GitRepoNameRule;

        try {
            $result = $rule->passes('repo', $value);
        } catch (\TypeError) {
            $result = false;
        }

        $this->assertFalse($result);
    }

    public function validRepoNames(): array
    {
        return [
            ['user/repo'],
            ['user//repo'],
            ['userName/repoName'],
            ['user_name/repo_name'],
            ['megaman666/spaghetti123code'],
            ['true'],
            ['false'],

            // TODO: IMPORTANT! Update the rule so these don't pass:
            ['foobar'],
            ['user/123'],
            ['123/user'],
            ['user/repouser2/repo2'],
        ];
    }

    public function invalidRepoNames(): array
    {
        return [
            ['user repo'],
            ['фу/бар'],
            ['user/repo, user2/repo2'],
            ['user/repo user2/repo2'],
            [null],
            [123],
            [1.1],
            [true],
            [false],
            [[]],
            [['']],
            [['user/repo']],
        ];
    }
}
