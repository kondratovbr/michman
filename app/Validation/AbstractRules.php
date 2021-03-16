<?php declare(strict_types=1);

namespace App\Validation;

use App\Validation\Exceptions\InvalidRule;
use App\Validation\Exceptions\RuleAlreadyAdded;
use App\Validation\Exceptions\UnknownRule;
use App\Support\Traits\ArrayableBasicImmutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class AbstractRules implements Arrayable, \ArrayAccess, \Iterator, \Countable
{
    use ArrayableBasicImmutable;

    /*
     * TODO: I can probably wrap this whole system into a nice little Laravel package for open-source.
     *   But first, I should probably refactor it into a more OOP style, i.e. make rules into separate classes,
     *   so instead of doing "Rules::image()", user will do "new Rules\Image()". This way the system will be OOP-style expandable.
     *   Don't forget to cover with tests.
     *   I can also wrap it into an even bigger abstraction layer, making it possible to abstract whole fieldsets.
     */

    // TODO: Add the rest of the built-in rules from Laravel. Is there a list built-in? With ordering? Probably not, but worth checking.
    // TODO: Make it possible to add more than one copy of the same named rule.

    /**
     * All of the named rules recognised by this class
     *
     * Rules will be applied in the same order as in this array. lowercase only. Usable for applying 'bail' rule.
     *
     * @var string[]
     */
    protected const NAMED_RULES = [
        'bail', 'required', 'nullable', 'required_with', 'required_without', 'required_without_all',
        'array', 'string', 'integer', 'numeric', 'alpha', 'alpha_dash', 'alpha_num', 'bool', 'boolean', 'file', 'image', 'uuid',
        'size', 'min', 'max',
        'regex', 'confirmed',
        'mimetypes', 'in', 'exists', 'unique',
    ];

    /**
     * Actual named (built-in) rules that will be applied
     *
     * Syntax: ruleName => ruleAttributes,
     * i.e. 'exists' => 'users,id'
     *
     * @var string[]
     */
    protected array $namedRules = ['name' => 'rule'];

    /**
     * Actual custom rules (objects) that will be applied
     *
     * @var Rule[]
     */
    protected array $ruleInstances = [];

    public function __construct(string|array|null $rules = null)
    {
        // Initialize the actual set of rules
        foreach (static::NAMED_RULES as $RULE)
            $this->namedRules[$RULE] = null;

        // Add rules provided
        foreach (Arr::wrap($rules) as $rule) {
            $this->addRule($rule);
        }
    }

    /**
     * Add the rule to the list of rules.
     */
    public function addRule(string|object $rule): static
    {
        if ($this->hasRule($rule))
            throw new RuleAlreadyAdded($rule);

        // Simple Laravel rules represented as strings, example: 'exists:users,id'.
        if (is_string($rule))
            return $this->addNamedRule($rule);

        // Built-in Laravel rule class helpers, like Illuminate\Validation\Rules\In, which are intended to be converted to strings.
        if (method_exists($rule, '__toString'))
            return $this->addNamedRule((string) $rule);

        // Custom rules
        if ($rule instanceof Rule)
            return $this->addRuleInstance($rule);

        throw new InvalidRule($rule);
    }

    /**
     * Check if this instance has the rule already.
     * Non-strict check, i.e. without attributes.
     */
    public function hasRule(string|object $rule): bool
    {
        if (is_string($rule))
            return $this->hasNamedRule($rule);

        if (method_exists($rule, '__toString'))
            return $this->hasNamedRule((string) $rule);

        if ($rule instanceof Rule)
            return $this->hasRuleInstance($rule);

        throw new InvalidRule($rule);
    }

    private function hasNamedRule(string $rule): bool
    {
        $this->validateNamedRule($rule);

        return $this->namedRules[$this->ruleName($rule)] != null;
    }

    private function hasRuleInstance(Rule $rule): bool
    {
        $ruleType = get_class($rule);

        foreach ($this->ruleInstances as $addedRule) {
            if (get_class($addedRule) == $ruleType)
                return true;
        }

        return false;
    }

    private function addNamedRule(string $rule): static
    {
        $this->validateNamedRule($rule);

        $this->namedRules[$this->ruleName($rule)] = $this->ruleAttributes($rule);

        return $this;
    }

    private function addRuleInstance(Rule $rule): static
    {
        $this->ruleInstances[] = $rule;

        return $this;
    }

    private function validateNamedRule(string $rule): void
    {
        if (! in_array($this->ruleName($rule), static::NAMED_RULES))
            throw new UnknownRule($rule);
    }

    private function ruleName(string $rule): string
    {
        return Str::lower(explode(':', $rule)[0]);
    }

    private function ruleAttributes(string $rule): string
    {
        // Empty string will be returned for rules with no attributes, like 'required' for example.
        return explode(':', $rule)[1] ?? '';
    }

    public function toArray(): array
    {
        $namedRules = [];

        foreach ($this->namedRules as $name => $attributes) {
            if (! is_null($attributes))
                $namedRules[] = $name . ($attributes == '' ? '' : ':' . $attributes);
        }

        return array_merge($namedRules, $this->ruleInstances);
    }
}
