<?php declare(strict_types=1);

namespace App\Validation;

/*
 * TODO: CRITICAL! Don't forget to cover this whole system with tests.
 */

/*
 * NOTE: Actual built-in Laravel validation rules
 * are in Illuminate\Validation\Concerns\ValidatesAttributes class.
 */

use App\Rules\DomainRule;
use App\Rules\GitRepoNameRule;
use App\Rules\SshPublicKeyRule;
use App\Rules\UnixAbsolutePathRule;
use App\Rules\UnixRelativePathRule;

/**
 * Support class Rules.
 *
 * Generates and modifies validation rules arrays.
 */
class Rules extends AbstractBaseRules
{
    static public function currentUserPassword(): static
    {
        // We don't validate against the lower length limit just so we
        // can display a more appropriate validation message.
        return (new static([
            'string',
            'password'
        ]))->max((int) config('auth.password.max_length'));
    }

    /**
     * Default validation rules for weird HTML checkboxes.
     */
    static public function checkbox(): static
    {
        return (new static([
            'string',
            'size:2',
            'in:on',
        ]))->nullable();
    }

    /**
     * Default validation rules for all sorts of strings.
     */
    static public function string(int|null $minLength = null, int|null $maxLength = null): static
    {
        return (new static('string'))->lengthBetween($minLength, $maxLength);
    }

    /**
     * Default validation rules for alphanumeric strings with dashes and underscores.
     *
     * https://en.wikipedia.org/wiki/Alphanumeric
     */
    static public function alphaNumDashString(int|null $minLength = null, int|null $maxLength = null): static
    {
        return (new static(['string', 'alpha_dash']))->lengthBetween($minLength, $maxLength);
    }

    /**
     * Validation rules for strings used as a user's password.
     */
    static public function genericPassword(): static
    {
        return static::string()
            ->min((int) config('auth.password.min_length'))
            ->max((int) config('auth.password.max_length'));
    }

    static public function integer(int|null $min = null, int|null $max = null): static
    {
        return (new static('integer'))->minMax($min, $max);
    }

    static public function numeric(float|null $min = null, float|null $max = null): static
    {
        return (new static([
            'string',
            'numeric'
        ]))->minMax($min, $max);
    }

    static public function array(int|null $min = null, int|null $max = null): static
    {
        return (new static('array'))->lengthBetween($min, $max);
    }

    static public function boolean(): static
    {
        return new static('bool');
    }

    static public function url(): static
    {
        return new static([
            'string',
            'min:1',
            'max:255',
            'url'
        ]);
    }

    static public function uuid(): static
    {
        return new static([
            'string',
            'uuid',
            'max:128',
        ]);
    }

    static public function timestamp(): static
    {
        return new static([
            'integer',
            'min:0',
        ]);
    }

    static public function email(): static
    {
        return new static([
            'string',
            'max:255',
            'email',
        ]);
    }

    static public function ip(): static
    {
        return new static([
            'string',
            'min:4',
            'max:40',
            'ip',
        ]);
    }

    static public function domain(): static
    {
        return (new static([
            'string',
            'min:2',
            'max:255',
        ]))->addRule(new DomainRule);
    }

    static public function absolutePath(): static
    {
        return (new static([
            'string',
            'min:1',
            'max:255',
        ]))->addRule(new UnixAbsolutePathRule);
    }

    static public function relativePath(): static
    {
        return (new static([
            'string',
            'min:1',
            'max:255',
        ]))->addRule(new UnixRelativePathRule);
    }

    static public function gitRepoName(): static
    {
        return (new static([
            'string',
            'min:1',
            'max:255',
        ]))->addRule(new GitRepoNameRule);
    }

    static public function sshPublicKey(): static
    {
        return (new static([
            'string',
            'min:1',
            'max:2048',
        ]))->addRule(new SshPublicKeyRule);
    }
}
