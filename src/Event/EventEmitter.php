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
    public function emit($event): object
    {
        if (is_string($event)) {
            $event = $this->generateEventFromString($event);
        }

        $reflection = new ReflectionObject($event);
        $wrappedIt = $reflection->hasMethod(EventListenersMapper::DEFAULT_TAG_METHOD)
            || ($event instanceof EventInterface);
        if (!$wrappedIt) {
            $event = new Event($event);
        }
        return $this->eventDispatcher->dispatch($event);
    }

    /**
     * Generate anonymous event from string.
     *
     * @param string $event
     * @return EventHasTypeInterface
     *
     */
    protected function generateEventFromString(string $event)
    {
        return new class($event) implements EventInterface, EventHasTypeInterface {
            /**
             * @var string
             */
            private $event;

            public function __construct(string $event)
            {
                $this->event = $event;
            }

            /**
             * @inheritDoc
             */
            public function eventType(): string
            {
                return $this->event;
            }

            /**
             * @inheritDoc
             */
            public function tag(): string
            {
                return lcfirst(
                    str_replace(
                        ' ',
                        '',
                        ucwords(preg_replace('/[^a-zA-Z0-9]/', ' ', $this->event))
                    )
                    . 'Handler'
                );
            }
        };
    }
}
