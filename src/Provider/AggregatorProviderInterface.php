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
     * @param ListenerEventTypeProviderInterface $provider
     * @return $this
     */
    public function subscribeProvider(ListenerEventTypeProviderInterface $provider): self;

    /**
     * Unsubscribe event/listeners mapper from aggregator.
     *
     * @param ListenerEventTypeProviderInterface $provider
     * @return $this
     */
    public function unsubscribeProvider(ListenerEventTypeProviderInterface $provider): self;
}
