<?php


namespace Pinoven\Dispatcher\Samples;

class EventMapperProviderSampleD extends EventListenersMapperSampleDefault
{

    protected $priority = 10;

    public function getEventType(): string
    {
        return EventSampleB::class;
    }

    public function mapListeners(): iterable
    {
        return [
            new class {
                public function eventB_test_handler(EventSampleB $eventSampleB)
                {
                    $eventSampleB->increment();
                }
            },
            function (EventSampleB $eventSampleB) {
                $eventSampleB->increment();
                $eventSampleB->stopPropagation();
            },
            ListenerSampleA::class
        ];
    }
}
