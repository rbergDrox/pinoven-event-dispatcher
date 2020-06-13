<?php


namespace Pinoven\Dispatcher\Event;

use Fig\EventDispatcher\TaggedProviderTrait;
use Pinoven\Dispatcher\Listener\ProxyListener;
use Pinoven\Dispatcher\Priority\ItemPriorityInterface;

/**
 * Class EventMapperProvider
 * @package Pinoven\Dispatcher\Event
 */
abstract class EventMapperProvider implements EventListenersMapperInterface, ItemPriorityInterface
{
    use TaggedProviderTrait;

    /** @var string Default tag method listener should implement to to be in right event list. */
    const DEFAULT_TAG = 'handler';

    /** @var string Default method name the event should implement to find the event tag method. */
    const DEFAULT_TAG_METHOD = 'tag';

    /**
     * @var ProxyListener
     */
    protected $proxyListener;

    /** @var int
     */
    protected $priority = 0;

    /**
     * EventMapperProvider constructor.
     *
     * @param ProxyListener $proxyListener
     */
    public function __construct(ProxyListener $proxyListener)
    {
        $this->proxyListener = $proxyListener;
    }

    /**
     * @inheritDoc
     */
    public function getProxyListener(): ProxyListener
    {
        return $this->proxyListener;
    }

    /**
     * @inheritDoc
     */
    abstract public function mapListeners(): iterable;

    /**
     * @inheritDoc
     */
    abstract public function getEventType(): string;

    /**
     * @inheritDoc
     */
    public function getTagMethod(): string
    {
        return self::DEFAULT_TAG_METHOD;
    }

    /**
     * @inheritDoc
     */
    protected function eventType(): string
    {
        return $this->getEventType();
    }

    /**
     * @inheritDoc
     */
    protected function tagMethod(): string
    {
        return $this->getTagMethod();
    }


    /**
     * Use to filter listeners list by tag and event type by using getEventType() method.
     *
     * @param iterable $listeners
     * @param string $tag
     * @return iterable
     */
    private function filterListenersByTag(iterable $listeners, string $tag): iterable
    {
        return $this->getProxyListener()->getCallableListeners($this->getEventType(), $listeners, $tag);
    }

    /**
     * @inheritDoc
     */
    protected function getListenersForTag(string $tag): iterable
    {
        $listenersForTags = $this->filterListenersByTag($this->mapListeners(), $tag);
        yield $this->getEventType() => $listenersForTags;
    }

    /**
     * @inheritDoc
     */
    protected function getListenersForAllTags(): iterable
    {
        yield $this->getEventType() => $this->filterListenersByTag($this->mapListeners(), self::DEFAULT_TAG);
        yield $this->getEventType() => $this->filterListenersByTag($this->mapListeners(), '__invoke');
    }

    /**
     * @inheritDoc
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @inheritDoc
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

}
