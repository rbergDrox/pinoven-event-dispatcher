<?php


namespace Pinoven\Dispatcher\Samples;

class EventMapperProviderSampleE extends EventMapperProviderSampleDefault
{
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
            ListenerSampleB::class
        ];
    }
}