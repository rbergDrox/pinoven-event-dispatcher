<?php


namespace Pinoven\Dispatcher\Samples;

use Pinoven\Dispatcher\Event\EventListenersMapper;

class EventListenersMapperSampleDefault extends EventListenersMapper
{
    public function getEventType(): string
    {
        return EventSampleA::class;
    }


    /**
     * @inheritDoc
     */
    public function mapListeners(): iterable
    {
        return [
            new class {
                public function handler(EventSampleA $eventSample)
                {
                }
            },
            'eventListenerInvoked',
            ListenerSampleA::class,
            new ListenerSampleB(),
            [new ListenerSampleC()],
            new class {
                public function eventB_test_handler(EventSampleB $eventSample)
                {
                }
            },
            new ListenerSampleD()
        ];
    }
}
