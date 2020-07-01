<?php


namespace Pinoven\Dispatcher\Event;

/**
 * Interface EventHasTypeInterface
 * @package Pinoven\Dispatcher\Event
 */
interface EventHasTypeInterface
{

    /**
     * Returns the class or interface type this provider is for.
     * @return string
     */
    public function eventType(): string;
}
