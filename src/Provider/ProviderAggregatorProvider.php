<?php


namespace Pinoven\Dispatcher\Provider;

use Fig\EventDispatcher\AggregateProvider;

/**
 * Class ProviderAggregatorProvider
 * @package Pinoven\Dispatcher\Provider
 */
class ProviderAggregatorProvider extends AggregateProvider implements AggregatorProviderInterface
{

    /**
     * @inheritDoc
     */
    public function subscribeProvider(ListenerEventTypeProviderInterface $provider): AggregatorProviderInterface
    {
        return $this->addProvider($provider);
    }

    /**
     * @inheritDoc
     */
    public function unsubscribeProvider(ListenerEventTypeProviderInterface $provider): AggregatorProviderInterface
    {
        if (($key = array_search($provider, $this->providers)) !== false) {
            unset($this->providers[$key]);
        }
        return $this;
    }
}
