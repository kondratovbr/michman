<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * SshKey Eloquent model
 */
class SshKey extends AbstractModel
{
    use HasFactory;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        //
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [
        //
    ];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [
        //
    ];

    /** @var string[] The accessors to append to the model's array form. */
    protected $appends = [
        //
    ];

    /** @var string[] The event map for the model. */
    protected $dispatchesEvents = [
        //
    ];
}
