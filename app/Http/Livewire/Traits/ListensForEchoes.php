<?php declare(strict_types=1);

namespace App\Http\Livewire\Traits;

use App\Support\Arr;

trait ListensForEchoes
{
    private array $broadcastListeners = [];

    /**
     * Set up Echo broadcast listeners.
     */
    abstract protected function configureEchoListeners(): void;

    /**
     * Listen for a broadcasted event on an open channel.
     */
    protected function echoOpen(string $channelName, string $eventClass, string $method): void
    {
        // Syntax for open channels:
        // 'echo:CHANNEL_NAME,.FULLY_QUALIFIED_EVENT_CLASS_NAME' => 'METHOD_NAME',
        // 'echo:foo-channel,.App\Events\FoobarEvent' => 'foobar',
        // NOTE: Dot is not a typo. Livewire prepends the event name with the default event namespace if omitted.
        $this->broadcastListeners["echo:{$channelName},.{$eventClass}"] = $method;
    }

    /**
     * Listen for a broadcasted event on a private channel.
     */
    protected function echoPrivate(string $channelName, string $eventClass, string $method): void
    {
        // Syntax for private channels:
        // 'echo-private:CHANNEL_NAME,.FULLY_QUALIFIED_EVENT_CLASS_NAME' => 'METHOD_NAME',
        // 'echo-private:foo-channel,.App\Events\FoobarEvent' => 'foobar',
        // NOTE: Dot is not a typo. Livewire prepends the event name with the default event namespace if omitted.
        $this->broadcastListeners["echo-private:{$channelName},.{$eventClass}"] = $method;
    }

    /**
     * Listen for a broadcasted event on a presence channel.
     */
    protected function echoPresence(string $channelName, string $eventClass, string $method): void
    {
        // Syntax for presence channels:
        // 'echo-presence:CHANNEL_NAME,.FULLY_QUALIFIED_EVENT_CLASS_NAME' => 'METHOD_NAME',
        // 'echo-presence:foo-channel,.App\Events\FoobarEvent' => 'foobar',
        // NOTE: Dot is not a typo. Livewire prepends the event name with the default event namespace if omitted.
        $this->broadcastListeners["echo-presence:{$channelName},.{$eventClass}"] = $method;
    }

    protected function getListeners(): array
    {
        $this->configureEchoListeners();

        return Arr::merge(
            $this->listeners ?? [],
            $this->broadcastListeners,
        );
    }
}
