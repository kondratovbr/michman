<?php declare(strict_types=1);

namespace App\Validation;

use App\Exceptions\NotModelException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

abstract class AbstractBaseRules extends AbstractRules
{
    public function required(): static
    {
        return $this->addRule('required');
    }

    public function nullable(): static
    {
        return $this->addRule('nullable');
    }

    public function bail(): static
    {
        return $this->addRule('bail');
    }

    public function min(int|float $value): static
    {
        if (! is_numeric($value))
            throw new \RuntimeException('Tried to add a "min" validation rule with non-numeric argument.');

        return $this->addRule('min:' . $value);
    }

    public function max(int|float $value): static
    {
        if (! is_numeric($value))
            throw new \RuntimeException('Tried to add a "max" validation rule with non-numeric argument.');

        return $this->addRule('max:' . $value);
    }

    public function in(array $values): static
    {
        // TODO: Make this method work similar to the built-in Rule::in. Look into it.

        return $this->addRule(Rule::in($values));
    }

    /**
     * Request must contain a field named FIELD_confirmation,
     * where FIELD is the name of this field,
     * and these two fields must be identical.
     */
    public function confirmed(): static
    {
        return $this->addRule('confirmed');
    }

    /**
     * Field must be between $minLength and $maxLength long.
     *
     * Applicable for strings and arrays.
     */
    public function lengthBetween(?int $minLength = null, ?int $maxLength = null): static
    {
        return $this->minMax($minLength, $maxLength);
    }

    /**
     * Field must be of a specified length (for strings).
     */
    public function lengthExactly(int $length): static
    {
        return $this->minMax($length, $length);
    }

    /**
     * Add built-in "min" and "max" rules.
     */
    public function minMax(int|float|null $min = null, int|float|null $max = null): static
    {
        if (! is_null($min))
            $this->min($min);

        if (! is_null($max))
            $this->max($max);

        return $this;
    }

    /**
     * Field must be a string (or a number, which will be casted to a string)
     * and match a regex provided.
     */
    public function regex(string $pattern): static
    {
        return $this->addRule('regex:' . $pattern);
    }

    /**
     * Required if another field is present.
     */
    public function requiredWith(string $field): static
    {
        return $this->requiredWithAny($field);
    }

    /**
     * Required if at least one of the fields is present.
     */
    public function requiredWithAny(string|array $fields): static
    {
        return $this->addRule('required_with:' . implode(',', Arr::wrap($fields)));
    }

    /**
     * Required only when any of the specified fields aren't present.
     */
    public function requiredWithoutAny(string|array $fields): static
    {
        return $this->addRule('required_without:' . implode(',', Arr::wrap($fields)));
    }

    /**
     * Required only when none of the specified fields are present.
     *
     * I.e. when any of the fields is present this field will not be required.
     */
    public function requiredWithoutAll(string|array $fields): static
    {
        return $this->addRule('required_without_all:' . implode(',', Arr::wrap($fields)));
    }

    /**
     * Use a condition to determine if the field is required or not.
     *
     * If a closure is passed, it will be resolved through a service container.
     */
    public function requiredIf(bool|\Closure $condition): static
    {
        if ($this->checkCondition($condition))
            $this->required();

        return $this;
    }

    /**
     * Use a condition to determine if the field can be nullable or not.
     *
     * If a closure is passed, it will be resolved through a service container.
     */
    public function nullableUnless(bool|\Closure $condition): static
    {
        if(! $this->checkCondition($condition))
            $this->nullable();

        return $this;
    }

    /**
     * Must exist in the DB in the specified table and column.
     */
    public function existsInDb(string $table, string $column): static
    {
        return $this->addRule(Rule::exists($table, $column));
    }

    /**
     * Must not exist in the DB in the specified table and column.
     */
    public function doesNotExistInDb(string $table, string $column): static
    {
        return $this->addRule(Rule::unique($table, $column));
    }

    /**
     * Must exist in the DB in the specified column of the table for the specified model.
     */
    public function modelExistsInDb(string $modelClass, string $column): static
    {
        return $this->addRule(Rule::exists($modelClass, $column));
    }

    /**
     * Must not exist in the DB in the specified column of the table of the specified model.
     */
    public function modelDoesNotExistInDb(string $modelClass, string $column): static
    {
        return $this->addRule(Rule::unique($modelClass, $column));
    }

    /**
     * Model, identified by this field, must exist in the DB.
     *
     * Uses model route key by default.
     */
    public function modelExists(string $type, string $column = null): static
    {
        $model = new $type;

        if (! $model instanceof Model)
            throw new NotModelException($type);

        return $this->addRule(Rule::exists(
            $model->getTable(),
            $column ?? $model->getRouteKeyName()
        ));
    }

    /**
     * Check a potentially callable condition.
     */
    private function checkCondition($condition): bool
    {
        if (is_callable($condition))
            $condition = app()->call($condition);

        return (bool) $condition;
    }
}
