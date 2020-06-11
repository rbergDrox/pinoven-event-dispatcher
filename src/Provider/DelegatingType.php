<?php


namespace Pinoven\Dispatcher\Provider;

use Fig\EventDispatcher\DelegatingProvider as FigDelegatingProvider;
use Pinoven\Dispatcher\Event\EventTypeMapperInterface;

/**
 * Class DelegatingType
 * @package Pinoven\Dispatcher\Provider
 */
class DelegatingType extends FigDelegatingProvider implements ListenerEventTypeProviderInterface
{
    /**
     * @inheritDoc
     */
    public function subscribeEventTypeMapper(EventTypeMapperInterface $provider): ListenerEventTypeProviderInterface
    {
        return $this->addProvider($provider, [$provider->getEventType()]);
    }

    /**
     * @inheritDoc
     */
    public function unsubscribeEventTypeMapper(EventTypeMapperInterface $provider): ListenerEventTypeProviderInterface
    {
        if (($key = array_search($provider, $this->providers[$provider->getEventType()])) !== false) {
            unset($this->providers[$provider->getEventType()][$key]);
        }
        return $this;
    }
}
