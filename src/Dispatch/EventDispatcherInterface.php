<?php


namespace Pinoven\Dispatcher\Dispatch;

use Pinoven\Dispatcher\Provider\AggregatorProviderInterface;
use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;

/**
 * Interface EventDispatcherInterface
 * @package Pinoven\Dispatcher\Dispatch
 */
interface EventDispatcherInterface extends PsrEventDispatcherInterface
{

    /**
     * Set the aggregator Listener Provider.
     *
     * @param AggregatorProviderInterface $aggregator
     * @return $this
     */
    public function setAggregator(AggregatorProviderInterface $aggregator): self;
}
