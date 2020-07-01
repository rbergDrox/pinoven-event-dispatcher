<?php


namespace Pinoven\Dispatcher\Event;

use PHPUnit\Framework\TestCase;
use Pinoven\Dispatcher\Samples\EventSampleA;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class EventEmitterTest
 * @package Pinoven\Dispatcher\Event
 */
class EventEmitterTest extends TestCase
{

    /**
     * @var EventDispatcherInterface|__anonymous@359
     */
    private $dispatcher;

    public function setUp(): void
    {
        $this->dispatcher = new class implements EventDispatcherInterface {

            public function dispatch(object $event)
            {
                return $event;
            }
        };
    }

    public function testEmitEventTaggedObject()
    {
        $event = new EventSampleA();
        $emitter = new EventEmitter($this->dispatcher);
        $emittedEvent = $emitter->emit($event);
        $this->assertEquals($event->tag(), $emittedEvent->tag());
        $this->assertNotInstanceOf(EventInterface::class, $emittedEvent);
    }

    public function testEmitEventNonTaggedObject()
    {
        $event =  new class{
        };

        $emitter = new EventEmitter($this->dispatcher);
        $emittedEvent = $emitter->emit($event);
        $this->assertEquals(EventListenersMapper::DEFAULT_TAG, $emittedEvent->tag());
        $this->assertInstanceOf(EventInterface::class, $emittedEvent);
    }

    public function testEmitEventWithString()
    {

        $event = 'custom.event';
        $emitter = new EventEmitter($this->dispatcher);
        $emittedEvent = $emitter->emit($event);
        $this->assertEquals('customEventHandler', $emittedEvent->tag());
        $this->assertInstanceOf(EventInterface::class, $emittedEvent);
        $this->assertInstanceOf(EventHasTypeInterface::class, $emittedEvent);
        $this->assertEquals($event, $emittedEvent->eventType());
    }
}
