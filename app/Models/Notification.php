<?php declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasModelHelpers;
use App\Models\Traits\UsesCamelCaseAttributes;
use App\Notifications\Interfaces\Viewable;
use Carbon\CarbonInterface;
use RuntimeException;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;

/**
 * @property string $id
 * @property string $type
 * @property Model $notifiable
 * @property array $data
 * @property CarbonInterface $readAt
 * @property CarbonInterface $createdAt
 * @property CarbonInterface $updatedAt
 */
class Notification extends DatabaseNotification
{
    use UsesCamelCaseAttributes,
        HasModelHelpers;

    /** Get the message to show in the UI. */
    public function getMessageAttribute(): string
    {
        return $this->type::message($this->data);
    }

    /** Get a notification-specific details view. */
    public function detailsView(): View
    {
        if (! $this->viewable())
            throw new RuntimeException('Tried to get a view of a non-viewable notification. Type: ' . $this->type);

        return $this->type::view($this->data);
    }

    /** Check if the underlying Notification has a detailed view in the UI. */
    public function viewable(): bool
    {
        return classImplements($this->type, Viewable::class);
    }
}
