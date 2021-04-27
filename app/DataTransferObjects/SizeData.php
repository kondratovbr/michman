<?php declare(strict_types=1);

namespace App\DataTransferObjects;

use Spatie\DataTransferObject\DataTransferObject;

class SizeData extends DataTransferObject
{
    public string $slug;
    public float $transfer;
    public float $priceMonthly;
    public int $memoryMb;
    public int $cpus;
    public int $diskGb;
    /** @var string[] */
    public array $regions;
    public bool $available;
    public string $description = '';
}
