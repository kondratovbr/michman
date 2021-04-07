<?php declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Lang extends Component
{
    public function __construct(
        private string $key
    ) {}

    /**
     * Get the requested locale-dependent view or a placeholder view
     * if the required one doesn't exist.
     */
    public function render(): View
    {
        $viewName = implode('.', [
            'lang',
            app()->getLocale(),
            $this->key,
        ]);

        return view()->exists($viewName)
            ? view($viewName)
            : view('lang.placeholder', ['key' => $this->key]);
    }
}
