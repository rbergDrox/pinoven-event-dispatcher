<?php


namespace Pinoven\Dispatcher\Event;

use ReflectionObject;

class EmittedEvent implements EventInterface
{
    /**
     * @var object
     */
    private $event;

    /**
     * EmittedEvent constructor.
     * @param object $event
     */
    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * @inheritDoc
     */
    public function tag(): string
    {
        $reflectionEvent =  new ReflectionObject($this->event);
        return $reflectionEvent->hasMethod('tag') ? $this->event->tag() : EventListenersMapper::DEFAULT_TAG;
    }
}
