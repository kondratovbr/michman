<?php declare(strict_types=1);

namespace App\Support;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\View\ViewFinderInterface;

class ConfigViewFactory extends ViewFactory
{
    /** @var string[] The extension to engine bindings. */
    protected $extensions = [
        'blade.txt' => 'blade',
        'blade.config' => 'blade',
    ];

    public function __construct(
        EngineResolver $engines,
        ViewFinderInterface $finder,
        Dispatcher $events,
        array $extensions = [],
    ) {
        parent::__construct($engines, $finder, $events);

        $this->extensions = Arr::mapAssoc($extensions, fn($index, $extension) => [$extension, 'blade']);
    }

    /**
     * Get a compiled and rendered view from a config file template.
     */
    public function render(string $view, array $data = []): string
    {
        return $this->make($view, $data)->render();
    }
}
