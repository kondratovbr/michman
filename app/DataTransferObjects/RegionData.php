<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class RegionData extends DataTransferObject
{
    public string $name;
    public string $slug;
    /** @var string[] */
    public array $sizes;
    public bool $available;
}
