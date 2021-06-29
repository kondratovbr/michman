<?php declare(strict_types=1);

namespace App\Http\Livewire\Traits;

use App\Support\Arr;

/*
 * TODO: Maybe think about refactoring broadcasting channels into channel classes, if there's ever too many of them.
 */

trait ListensForEchoes
{
    private array $broadcastListeners = [];

    /**
     * Set up Echo broadcast listeners.
     */
    abstract protected function configureEchoListeners(): void;

    /**
     * Listen for broadcasted events on an open channel.
     *
     * @param string|string[] $eventClasses
     */
    protected function echoOpen(string $channelName, array|string $eventClasses, string $method): void
    {
        foreach (Arr::wrap($eventClasses) as $eventClass) {
            // Syntax for open channels:
            // 'echo:CHANNEL_NAME,.FULLY_QUALIFIED_EVENT_CLASS_NAME' => 'METHOD_NAME',
            // 'echo:foo-channel,.App\Events\FoobarEvent' => 'foobar',
            // NOTE: Dot is not a typo. Livewire prepends the event name with the default event namespace if omitted.
            $this->broadcastListeners["echo:{$channelName},.{$eventClass}"] = $method;
        }
    }

    /**
     * Listen for a broadcasted event on a private channel.
     *
     * @param string|string[] $eventClasses
     */
    protected function echoPrivate(string $channelName, array|string $eventClasses, string $method): void
    {
        foreach (Arr::wrap($eventClasses) as $eventClass) {
            // Syntax for private channels:
            // 'echo-private:CHANNEL_NAME,.FULLY_QUALIFIED_EVENT_CLASS_NAME' => 'METHOD_NAME',
            // 'echo-private:foo-channel,.App\Events\FoobarEvent' => 'foobar',
            // NOTE: Dot is not a typo. Livewire prepends the event name with the default event namespace if omitted.
            $this->broadcastListeners["echo-private:{$channelName},.{$eventClass}"] = $method;
        }
    }

    /**
     * Listen for a broadcasted event on a presence channel.
     *
     * @param string|string[] $eventClasses
     */
    protected function echoPresence(string $channelName, array|string $eventClasses, string $method): void
    {
        foreach (Arr::wrap($eventClasses) as $eventClass) {
            // Syntax for presence channels:
            // 'echo-presence:CHANNEL_NAME,.FULLY_QUALIFIED_EVENT_CLASS_NAME' => 'METHOD_NAME',
            // 'echo-presence:foo-channel,.App\Events\FoobarEvent' => 'foobar',
            // NOTE: Dot is not a typo. Livewire prepends the event name with the default event namespace if omitted.
            $this->broadcastListeners["echo-presence:{$channelName},.{$eventClass}"] = $method;
        }
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
