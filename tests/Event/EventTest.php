<?php


namespace Pinoven\Dispatcher\Event;

use PHPUnit\Framework\TestCase;
use Pinoven\Dispatcher\Samples\EventSampleA;

/**
 * Class EmittedEventTest
 * @package Pinoven\Dispatcher\Event
 */
class EventTest extends TestCase
{

    public function testOriginEventAndPayloadValue()
    {
        $event =  new EventSampleA();
        $emittedEvent = new Event($event, 22, array(11, 22));
        $this->assertEquals([22, [11, 22]], $emittedEvent->getPayload());
        $this->assertEquals($event, $emittedEvent->getOriginEvent());
    }

    public function testEventWithTagMethod()
    {
        $event =  new EventSampleA();
        $emittedEvent = new Event($event);
        $this->assertEquals($event->tag(), $emittedEvent->tag());
    }

    public function testEventWithoutTagMethod()
    {
        $event =  new class{
        };
        $emittedEvent = new Event($event);
        $this->assertEquals(EventListenersMapper::DEFAULT_TAG, $emittedEvent->tag());
    }
}
