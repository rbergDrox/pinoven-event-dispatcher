<?php


namespace Pinoven\Dispatcher\Samples;

class EventSampleA extends DefaultEvent
{
    public function tag()
    {
        return 'eventA_test_handler';
    }
}
