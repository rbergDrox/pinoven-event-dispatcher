<?php


namespace Pinoven\Dispatcher\Provider;

use Psr\EventDispatcher\ListenerProviderInterface as PsrListenerProviderInterface;

/**
 * Interface ListenerProviderAggregatorInterface
 * @package Pinoven\Dispatcher\Provider
 */
interface AggregatorProviderInterface extends PsrListenerProviderInterface
{

    /**
     * Subscribe event/listeners mapper to aggregate.
     *
     * @param EventListenersSubscriberInterface $provider
     * @return $this
     */
    public function subscribe(EventListenersSubscriberInterface $provider): self;

    /**
     * Unsubscribe event/listeners mapper from aggregator.
     *
     * @param EventListenersSubscriberInterface $provider
     * @return $this
     */
    public function unsubscribe(EventListenersSubscriberInterface $provider): self;
}
