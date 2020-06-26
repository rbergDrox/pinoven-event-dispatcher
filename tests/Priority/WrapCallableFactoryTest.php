<?php


namespace Pinoven\Dispatcher\Priority;

use PHPUnit\Framework\TestCase;
use Pinoven\Dispatcher\Samples\EventSampleA;

class WrapCallableFactoryTest extends TestCase
{
    public function testWrapCallableFactory()
    {
        $event =  new EventSampleA();
        $callable = new class {
            public function __invoke(EventSampleA $eventSampleA)
            {
                $eventSampleA->increment();
            }
        };
        $wrapper = new WrapCallableFactory();
        $itemPriorityCallable = $wrapper->createWrapCallablePriority($callable, $event);
        $this->assertEquals(0, $itemPriorityCallable->getPriority());
    }
}
