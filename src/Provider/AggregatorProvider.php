<?php


namespace Pinoven\Dispatcher\Provider;

use Fig\EventDispatcher\AggregateProvider as FigAggregatorProvider;
use Pinoven\Dispatcher\Priority\PrioritizeInterface;

/**
 * Class AggregatorProvider
 * @package Pinoven\Dispatcher\Provider
 */
class AggregatorProvider extends FigAggregatorProvider implements AggregatorProviderInterface
{
    /**
     * @var PrioritizeInterface|null
     */
    private $prioritize;
    /**
     * @var bool
     */
    private $sortListeners;

    /**
     * AggregatorProvider constructor.
     * @param PrioritizeInterface|null $prioritize
     * @param bool $sortListeners
     */
    public function __construct(?PrioritizeInterface $prioritize = null, bool $sortListeners = false)
    {
        $this->prioritize = $prioritize;
        $this->sortListeners = $sortListeners;
    }

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

    /**
     * @inheritDoc
     */
    public function getListenersForEvent(object $event): iterable
    {
        if ($this->prioritize) {
            $sortedProviders = $this->prioritize->sortItems($this->providers);
            $this->providers = $sortedProviders;
        }
        $listeners = parent::getListenersForEvent($event);
        if ($this->prioritize && $this->sortListeners) {
            $listeners = $this->prioritize->sortItems($listeners);
        }
        return $listeners;
    }
}
