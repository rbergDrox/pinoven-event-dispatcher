<?php


namespace Pinoven\Dispatcher\Event;

use PHPUnit\Framework\TestCase;
use Pinoven\Dispatcher\Samples\EventSampleA;

/**
 * Class EmittedEventTest
 * @package Pinoven\Dispatcher\Event
 */
class EmittedEventTest extends TestCase
{
    public function testEventWithTagMethod()
    {
        $event =  new EventSampleA();
        $emittedEvent = new EmittedEvent($event);
        $this->assertEquals($event->tag(), $emittedEvent->tag());
    }

    public function testEventWithoutTagMethod()
    {
        $event =  new class{
        };
        $emittedEvent = new EmittedEvent($event);
        $this->assertEquals(EventListenersMapper::DEFAULT_TAG, $emittedEvent->tag());
    }
}
