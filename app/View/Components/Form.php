<?php declare(strict_types=1);

namespace App\View\Components;

use App\Support\Str;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Form extends Component
{
    public string|null $method;
    public bool $withFiles;

    public function __construct(string|null $method = null, bool $withFiles = false)
    {
        $this->method = is_null($method) ? null : Str::upper($method);
        $this->withFiles = $withFiles;
    }

    /**
     * Get the view that represents the component.
     */
    public function render(): View
    {
        return view('components.form');
    }
}
