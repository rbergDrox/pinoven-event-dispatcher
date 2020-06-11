<?php


namespace Pinoven\Dispatcher\Samples;

class ListenerSampleA
{
    public function handler(EventSampleA $eventSampleA)
    {
        $eventSampleA->increment();
    }
}
