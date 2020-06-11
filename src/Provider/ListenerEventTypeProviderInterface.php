<?php


namespace Pinoven\Dispatcher\Provider;

use Pinoven\Dispatcher\Event\EventTypeMapperInterface;
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
     * @param EventTypeMapperInterface $provider
     * @return $this
     */
    public function subscribeEventTypeMapper(EventTypeMapperInterface $provider): self;

    /**
     * Unsubscribe event/listeners mapper.
     *
     * @param EventTypeMapperInterface $provider
     * @return $this
     */
    public function unsubscribeEventTypeMapper(EventTypeMapperInterface $provider): self;
}
