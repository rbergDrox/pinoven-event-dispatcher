<?php


namespace Pinoven\Dispatcher\Event;

use ReflectionObject;

class Event implements EventInterface
{
    /**
     * @var object
     */
    private $originEvent;

    /**
     * EmittedEvent constructor.
     * @param object $originEvent
     */
    public function __construct(object $originEvent)
    {
        $this->originEvent = $originEvent;
    }

    /**
     * @inheritDoc
     */
    public function tag(): string
    {
        $reflectionEvent =  new ReflectionObject($this->originEvent);
        return $reflectionEvent->hasMethod('tag') ? $this->originEvent->tag() : EventListenersMapper::DEFAULT_TAG;
    }
}
