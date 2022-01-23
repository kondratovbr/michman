<?php declare(strict_types=1);

namespace App\Events\Interfaces;

use App\Models\Project;

interface ProjectEvent
{
    public function project(): Project|null;
}
