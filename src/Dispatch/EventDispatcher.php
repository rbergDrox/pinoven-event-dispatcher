<?php


namespace Pinoven\Dispatcher\Dispatch;

use Pinoven\Dispatcher\Provider\AggregatorProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;


/**
 * Class EventDispatcher
 * @package Pinoven\Dispatcher\Dispatch
 */
class EventDispatcher implements EventDispatcherInterface
{
    /** @var AggregatorProviderInterface */
    private $aggregator;

    /**
     * Dispatcher constructor.
     * @param AggregatorProviderInterface $aggregator
     */
    public function __construct(AggregatorProviderInterface $aggregator)
    {
        $this->aggregator = $aggregator;
    }


    /**
     * @inheritDoc
     */
    public function dispatch(object $event)
    {
        $listeners = $this->aggregator->getListenersForEvent($event);
        foreach ($listeners as $listener) {
            $listener($event);
            if (is_a($event, StoppableEventInterface::class) && $event->isPropagationStopped()) {
                break;
            }
        }
        return $event;
    }

    /**
     * @inheritDoc
     */
    public function setAggregator(AggregatorProviderInterface $aggregator): EventDispatcherInterface
    {
        $this->aggregator = $aggregator;
        return $this;
    }
}
