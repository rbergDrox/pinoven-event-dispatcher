<?php


namespace Pinoven\Dispatcher\Event;

use Fig\EventDispatcher\TaggedProviderTrait;
use Pinoven\Dispatcher\Listener\ProxyListener;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * Interface EventTypeMapperInterface
 * @package Pinoven\Dispatcher\Event
 */
interface EventTypeMapperInterface extends ListenerProviderInterface
{

    /**
     * Get event type. The class or interface type that this Provider is for.
     * @see TaggedProviderTrait::eventType();
     *
     * @return string
     */
    public function getEventType(): string;

    /**
     * The method to call on an event to get its tag.
     *  @see TaggedProviderTrait::tagMethod();
     *regarding
     * @return string
     */
    public function getTagMethod(): string;

    /**
     * Provide an iterable of Listeners. Listeners should be callable at the end.
     * @see https://www.php.net/manual/en/language.types.callable.php
     * @see TaggedProviderTrait::getListenersForEvent()
     *  will grab this list to return only the right listeners based on the event.
     * @see TaggedProviderTrait::getListenersForTag();
     * @see TaggedProviderTrait::getListenersForAllTags();
     *
     * @return iterable
     */
    public function mapListeners(): iterable;

    /**
     * Provide a class that permits callable or final callable from EventTypeMapperInterface::mapListeners().
     * It means if you provide something that's not callable for e.g "string" you can use the proxy and
     * write the logic to retrieve the callable.
     * @see EventTypeMapperInterface::mapListeners()
     *
     * @return ProxyListener
     */
    public function getProxyListener(): ProxyListener;
}
