<?php


namespace Pinoven\Dispatcher\Samples;

class EventMapperProviderSampleB extends EventListenersMapperSampleDefault
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
                public function eventA_test_handler(EventSampleB $eventSampleB)
                {
                    $eventSampleB->test = 0;
                }
            },
            new class {
                public function eventB_test_handler(EventSampleB $eventSampleB)
                {
                    $eventSampleB->test = 0;
                }
            },
            function (EventSampleB $eventSampleB, EventSampleA $eventSampleA) {
                $eventSampleB->test = 0;
                $eventSampleA->test = 0;
            }
        ];
    }
}
