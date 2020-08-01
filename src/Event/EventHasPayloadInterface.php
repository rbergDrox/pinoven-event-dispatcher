<?php


namespace Pinoven\Dispatcher\Event;

/**
 * Interface EventHasPayloadInterface
 * @package Pinoven\Dispatcher\Event
 */
interface EventHasPayloadInterface
{

    /**
     * Get the payload from event object.
     *
     * @return array
     */
    public function getPayload(): array;
}
