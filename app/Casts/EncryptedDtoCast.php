<?php declare(strict_types=1);

namespace App\Casts;

use App\DataTransferObjects\AbstractDto;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use App\Facades\Encryption;
use RuntimeException;

class EncryptedDtoCast implements CastsAttributes
{
    public function __construct(
        protected string $dtoClass,
    ) {}

    /**
     * Cast the given value retrieved from storage.
     *
     * @param Model $model
     * @param string $value
     */
    public function get($model, string $key, $value, array $attributes): AbstractDto
    {
        if (! is_string($value))
            throw new RuntimeException('Value received for EncryptedDtoCast::get() should be a string. Non-string or null provided.');

        $result = Encryption::decrypt($value);

        if (! $result instanceof AbstractDto)
            throw new RuntimeException('Object unserialized by EncryptedDtoCast::get() is not an instance of AbstractDto.');

        return $result;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model $model
     * @param AbstractDto|array $value
     */
    public function set($model, string $key, $value, array $attributes): string|null
    {
        if (is_null($value))
            throw new RuntimeException('Value provided for EncryptedDtoCast::set() should be either an instance of AbstractDto or an array. null provided.');

        if (is_array($value))
            $value = $this->dtoClass::fromArray($value);

        if (! $value instanceof AbstractDto)
            throw new RuntimeException('Value provided for EncryptedDtoCast::set() should be either an instance of AbstractDto or an array.');

        return Encryption::encrypt($value);
    }
}
