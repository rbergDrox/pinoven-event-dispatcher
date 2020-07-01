<?php


namespace Pinoven\Dispatcher\Event;

/**
 * Interface EventEmitterInterface
 * @package Pinoven\Dispatcher\Event
 *
 */
interface EventEmitterInterface
{

    /**
     * Emit an event and return the event object.
     * @param string|object $event
     * @return object|EventInterface|EventHasTypeInterface
     */
    public function emit($event): object;
}
