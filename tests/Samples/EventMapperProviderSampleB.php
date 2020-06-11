<?php


namespace Pinoven\Dispatcher\Samples;

class EventMapperProviderSampleB extends EventMapperProviderSampleDefault
{
    public function getEventType(): string
    {
        return EventSampleB::class;
    }

    public function mapListeners(): iterable
    {
        return [
            new class {
                public function eventA_test_handler(EventSampleB $eventSampleB)
                {
                }
            },
            new class {
                public function eventB_test_handler(EventSampleB $eventSampleB)
                {
                }
            },
            function (EventSampleB $eventSampleB, EventSampleA $eventSampleA) {
            }
        ];
    }
}
