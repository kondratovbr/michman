<?php declare(strict_types=1);

namespace App\Models;

use App\Casts\ForceBooleanCast;
use App\DataTransferObjects\WebhookCallExceptionDto;
use Carbon\CarbonInterface;
use Database\Factories\WebhookCallFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * WebhookCall Eloquent model
 *
 * @property int $id
 * @property string $type
 * @property string $url
 * @property array $headers
 * @property array $payload
 * @property WebhookCallExceptionDto|null $exception
 * @property bool $processed
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 *
 * @property-read Webhook $webhook
 *
 * @method static WebhookCallFactory factory(...$parameters)
 */
class WebhookCall extends AbstractModel
{
    use HasFactory;

    /** @var string[] The attributes that are mass assignable. */
    protected $fillable = [
        'type',
        'url',
        'headers',
        'payload',
        'exception',
        'processed',
    ];

    /** @var string[] The attributes that should be visible in arrays and JSON. */
    protected $visible = [];

    /** @var string[] The attributes that should be cast to native types with their respective types. */
    protected $casts = [
        'headers' => 'array',
        'payload' => 'array',
        'exception' => WebhookCallExceptionDto::class,
        'processed' => ForceBooleanCast::class,
    ];

    /** @var string[] The event map for the model. */
    protected $dispatchesEvents = [
        //
    ];

    /** Get a relation with the webhook this call was made for. */
    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class);
    }
}
