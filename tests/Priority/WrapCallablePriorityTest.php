<?php


namespace Pinoven\Dispatcher\Priority;

use PHPUnit\Framework\TestCase;
use Pinoven\Dispatcher\Samples\EventSampleA;

class WrapCallablePriorityTest extends TestCase
{

    public function testSetPriority()
    {
        $callable = function (EventSampleA $eventSampleA) {
            $eventSampleA->increment();
        };
        $itemPriorityCallable = new WrapCallablePriority($callable);
        $this->assertEquals(0, $itemPriorityCallable->getPriority());
        $itemPriorityCallable->setPriority(2);
        $this->assertEquals(2, $itemPriorityCallable->getPriority());
    }

    public function testInvokeCallable()
    {
        $event = new EventSampleA();
        $callable = function (EventSampleA $eventSampleA) {
            $eventSampleA->increment();
        };
        $itemPriorityCallable = new WrapCallablePriority($callable);
        $this->assertEquals(0, $event->getIncrement());
        $itemPriorityCallable($event);
        $this->assertEquals(1, $event->getIncrement());
    }
}
