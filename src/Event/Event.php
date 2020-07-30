<?php


namespace Pinoven\Dispatcher\Event;

use ReflectionObject;

class Event implements EventInterface, EventHasPayloadInterface
{
    /**
     * @var object
     */
    private $originEvent;

    /**
     * @var array
     */
    private $payload;

    /**
     * EmittedEvent constructor.
     * @param object $originEvent
     * @param array $payload
     */
    public function __construct(object $originEvent, ...$payload)
    {
        $this->originEvent = $originEvent;
        $this->payload = $payload;
    }

    /**
     * @inheritDoc
     */
    public function tag(): string
    {
        $reflectionEvent =  new ReflectionObject($this->originEvent);
        return $reflectionEvent->hasMethod('tag') ? $this->originEvent->tag() : EventListenersMapper::DEFAULT_TAG;
    }

    /**
     * @return object
     */
    public function getOriginEvent(): object
    {
        return $this->originEvent;
    }

    /**
     * @inheritDoc
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
}
