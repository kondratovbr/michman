<?php declare(strict_types=1);

namespace App\Notifications\Interfaces;

use Illuminate\Contracts\View\View;

interface Viewable
{
    /**
     * Get the details view to display this notification in the UI.
     */
    public static function view(array $data = []): View;
}
