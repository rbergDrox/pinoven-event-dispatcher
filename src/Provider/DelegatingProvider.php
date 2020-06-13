<?php


namespace Pinoven\Dispatcher\Provider;

use Fig\EventDispatcher\DelegatingProvider as FigDelegatingProvider;
use Pinoven\Dispatcher\Event\EventListenersMapperInterface;
use Pinoven\Dispatcher\Priority\PrioritizeInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * Class DelegatingProvider
 * @package Pinoven\Dispatcher\Provider
 */
class DelegatingProvider extends FigDelegatingProvider implements ListenerEventTypeProviderInterface
{
    /**
     * @var PrioritizeInterface|null
     */
    private $prioritize;

    /**
     * DelegatingProvider constructor.
     * @param ListenerProviderInterface|null $defaultProvider
     * @param PrioritizeInterface|null $prioritize
     */
    public function __construct(
        ?ListenerProviderInterface $defaultProvider = null,
        ?PrioritizeInterface $prioritize = null
    ) {
        parent::__construct($defaultProvider);
        $this->prioritize = $prioritize;
    }

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

    /**
     * @inheritDoc
     */
    public function getListenersForEvent(object $event): iterable
    {
        if ($this->prioritize) {
            foreach ($this->providers as $type => $providers) {
                if ($event instanceof $type) {
                    $sortedProviders = $this->prioritize->sortItems($providers);
                    $this->providers[$type] = $sortedProviders;
                }
            }
        }
        return parent::getListenersForEvent($event);
    }
}
