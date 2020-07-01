<?php


namespace Pinoven\Dispatcher\Event;

/**
 * Interface EventInterface
 * @package Pinoven\Dispatcher\Event
 */
interface EventInterface
{

    /**
     * Define the hook to handle an event.
     *
     * @return string
     */
    public function tag(): string;
}
