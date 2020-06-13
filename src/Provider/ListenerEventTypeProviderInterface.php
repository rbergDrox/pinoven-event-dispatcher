<?php


namespace Pinoven\Dispatcher\Provider;

use Pinoven\Dispatcher\Event\EventListenersMapperInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * Interface ListenerEventTypeProviderInterface
 * @package Pinoven\Dispatcher\Provider
 */
interface ListenerEventTypeProviderInterface extends ListenerProviderInterface
{

    /**
     * Subscribe event/listeners mapper.
     *
     * @param EventListenersMapperInterface $provider
     * @return $this
     */
    public function subscribe(EventListenersMapperInterface $provider): self;

    /**
     * Unsubscribe event/listeners mapper.
     *
     * @param EventListenersMapperInterface $provider
     * @return $this
     */
    public function unsubscribe(EventListenersMapperInterface $provider): self;
}
