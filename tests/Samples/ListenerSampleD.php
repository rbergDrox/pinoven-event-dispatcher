<?php


namespace Pinoven\Dispatcher\Samples;

use Pinoven\Dispatcher\Priority\ItemPriorityInterface;

class ListenerSampleD implements ItemPriorityInterface
{
    protected $priority = 0;

    public function __invoke(EventSampleB $eventSampleB)
    {
        $eventSampleB->test = 0;
    }

    /**
     * @inheritDoc
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @inheritDoc
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }
}
