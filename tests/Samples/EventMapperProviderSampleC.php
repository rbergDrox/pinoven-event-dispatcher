<?php


namespace Pinoven\Dispatcher\Samples;

class EventMapperProviderSampleC extends EventMapperProviderSampleDefault
{
    public function getEventType(): string
    {
        return EventSampleA::class;
    }

    public function mapListeners(): iterable
    {
        return [
            new class {
                public function eventA_test_handler(EventSampleB $eventSampleB)
                {
                }
            },
            function (EventSampleA $eventSampleA) {
            },
            ListenerSampleA::class
        ];
    }
}
