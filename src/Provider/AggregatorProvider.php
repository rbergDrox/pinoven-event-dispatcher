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
     * AggregatorProvider constructor.
     * @param PrioritizeInterface|null $prioritize
     */
    public function __construct(?PrioritizeInterface $prioritize = null)
    {
        $this->prioritize = $prioritize;
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
        return parent::getListenersForEvent($event);
    }
}
