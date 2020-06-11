<?php


namespace Pinoven\Dispatcher\Listener;

/**
 * Interface ProxyListener
 * @package Pinoven\Dispatcher\Listener
 */
interface ProxyListener
{
    /**
     * Filter an iterable listeners list based on the event type.
     *
     * @param string $eventType
     * @param iterable $listeners
     * @param string $tag
     * @return iterable
     */
    public function getCallableListeners(string $eventType, iterable $listeners, string $tag): iterable;

    /**
     * Retrieve/Construct a callable from any listener.
     * The listener is already a callable it should be return directly.
     *
     * @param string $eventType
     * @param mixed $listener
     * @param string $tag
     * @return callable|null
     */
    public function listenerToCallable(string $eventType, $listener, string $tag): ?callable;
}
