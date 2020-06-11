<?php


namespace Pinoven\Dispatcher\Samples;

use Psr\EventDispatcher\StoppableEventInterface;

class EventSampleB extends DefaultEvent implements StoppableEventInterface
{
    private $stop = false;

    public function tag()
    {
        return 'eventB_test_handler';
    }

    public function stopPropagation()
    {
        $this->stop = true;
    }

    /**
     * @inheritDoc
     */
    public function isPropagationStopped(): bool
    {
        return $this->stop;
    }
}
