<?php declare(strict_types=1);

namespace App\Events\Interfaces;

use App\Models\Project;

interface ProjectEvent
{
    /** Get the project that this was related to. */
    public function project(): Project|null;
}
