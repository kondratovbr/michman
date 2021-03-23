<?php declare(strict_types=1);

namespace App\View\Components\Box;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Form extends Component
{
    public string $formComponent;

    public function __construct(
        public string|null $method = null,
        public bool $withFiles = false,
        private string $type = 'basic',
    )
    {
        $this->formComponent = $this->getBaseFormComponentName($this->type);
    }

    /**
     * Get the name of the form component to use based on the type requested.
     */
    private function getBaseFormComponentName(string $type): string
    {
        return match ($type) {
            'vertical' => 'forms.vertical',
            'basic' => 'form',
            default => 'form',
        };
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('components.box.form');
    }
}
