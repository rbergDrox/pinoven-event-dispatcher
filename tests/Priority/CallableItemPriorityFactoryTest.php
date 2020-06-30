<?php


namespace Pinoven\Dispatcher\Priority;

use PHPUnit\Framework\TestCase;
use Pinoven\Dispatcher\Samples\EventSampleA;

class CallableItemPriorityFactoryTest extends TestCase
{
    public function testWrapCallableFactory()
    {
        $callable = new class {
            public function __invoke(EventSampleA $eventSampleA)
            {
                $eventSampleA->increment();
            }
        };
        $wrapper = new CallableItemPriorityFactory();
        $itemPriorityCallable = $wrapper->wrap($callable);
        $this->assertEquals(0, $itemPriorityCallable->getPriority());
    }
}
