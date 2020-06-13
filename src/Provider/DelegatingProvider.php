<?php


namespace Pinoven\Dispatcher\Provider;

use Fig\EventDispatcher\DelegatingProvider as FigDelegatingProvider;
use Pinoven\Dispatcher\Event\EventListenersMapperInterface;

/**
 * Class DelegatingProvider
 * @package Pinoven\Dispatcher\Provider
 */
class DelegatingProvider extends FigDelegatingProvider implements ListenerEventTypeProviderInterface
{
    /**
     * @inheritDoc
     */
    public function subscribe(EventListenersMapperInterface $provider): ListenerEventTypeProviderInterface
    {
        return $this->addProvider($provider, [$provider->getEventType()]);
    }

    /**
     * @inheritDoc
     */
    public function unsubscribe(EventListenersMapperInterface $provider): ListenerEventTypeProviderInterface
    {
        if (($key = array_search($provider, $this->providers[$provider->getEventType()])) !== false) {
            unset($this->providers[$provider->getEventType()][$key]);
        }
        return $this;
    }
}
