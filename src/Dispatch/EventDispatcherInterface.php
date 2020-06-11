<?php


namespace Pinoven\Dispatcher\Dispatch;


use Pinoven\Dispatcher\Provider\AggregatorProviderInterface;
use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;


interface EventDispatcherInterface extends PsrEventDispatcherInterface
{

    /**
     * @param AggregatorProviderInterface $aggregator
     * @return $this
     */
    public function setAggregator(AggregatorProviderInterface $aggregator): self;

}