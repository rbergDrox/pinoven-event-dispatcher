<?php


namespace Pinoven\Dispatcher\Event;

use Psr\EventDispatcher\EventDispatcherInterface;
use ReflectionObject;

class EventEmitter implements EventEmitterInterface
{

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * EventEmitter constructor.
     * @param EventDispatcherInterface $eventDispatcher
     *
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @inheritDoc
     */
    public function emit($event, ...$payload): object
    {
        if (is_string($event)) {
            $event = $this->generateCustomEventFromString($event, $payload);
        }

        $reflection = new ReflectionObject($event);
        $wrappedIt = $reflection->hasMethod(EventListenersMapper::DEFAULT_TAG_METHOD)
            || ($event instanceof EventInterface);
        if (!$wrappedIt) {
            $event = new Event($event, $payload);
        }
        return $this->eventDispatcher->dispatch($event);
    }

    /**
     * Generate a custom event from string.
     *
     * @param string $event
     * @param array $payload
     * @return EventInterface|EventHasTypeInterface
     */
    protected function generateCustomEventFromString(string $event, ...$payload): object
    {
        return new CustomEvent($event, $payload);
    }
}
