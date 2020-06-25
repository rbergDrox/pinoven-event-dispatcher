<?php


namespace Pinoven\Dispatcher\Samples;

class EventMapperProviderSampleC extends EventMapperProviderSampleDefault
{
    protected $priority = -2;

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
                    $eventSampleB->test = 0;
                }
            },
            function (EventSampleA $eventSampleA) {
                $eventSampleA->test = 0;
            },
            ListenerSampleA::class
        ];
    }
}
