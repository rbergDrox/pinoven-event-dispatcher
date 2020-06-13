<?php


namespace Pinoven\Dispatcher\Provider;

use PHPUnit\Framework\TestCase;
use Pimple\Container as PimpleContainer;
use Pimple\Psr11\Container;
use Pinoven\Dispatcher\Listener\ProxyListenersMapper;
use Pinoven\Dispatcher\Samples\EventMapperProviderSampleB;
use Pinoven\Dispatcher\Samples\EventMapperProviderSampleC;
use Pinoven\Dispatcher\Samples\EventMapperProviderSampleDefault;
use Pinoven\Dispatcher\Samples\EventSampleA;
use Pinoven\Dispatcher\Samples\ListenerSampleA;

class ProviderAggregatorTest extends TestCase
{
    /**
     * @var EventMapperProviderSampleB
     */
    private $eventMapperProviderB;

    /**
     * @var EventMapperProviderSampleC
     */
    private $eventMapperProviderC;

    /**
     * @var DelegatingProvider
     */
    private $delegatingProviderTypeA;

    /**
     * @var DelegatingProvider
     */
    private $delegatingProviderTypeB;

    /**
     * @var EventMapperProviderSampleDefault
     */
    private $eventMapperProviderDefault;
    /**
     * @var AggregatorProvider
     */
    private $providerAggregator;

    public function setUp(): void
    {
        $container = new Container(new PimpleContainer([
            'eventListenerInvoked' =>  new class {
                public function handler(EventSampleA $eventSampleA)
                {
                }
            },
            ListenerSampleA::class => new ListenerSampleA()
        ]));
        $proxy = new ProxyListenersMapper($container);
        $this->eventMapperProviderB = new EventMapperProviderSampleB($proxy);
        $this->eventMapperProviderC = new EventMapperProviderSampleC($proxy);
        $this->eventMapperProviderDefault = new EventMapperProviderSampleDefault($proxy);
        $this->delegatingProviderTypeA = new DelegatingProvider($this->eventMapperProviderDefault);
        $this->delegatingProviderTypeA->subscribe($this->eventMapperProviderB);
        $this->delegatingProviderTypeB = new DelegatingProvider($this->eventMapperProviderDefault);
        $this->delegatingProviderTypeB->subscribe($this->eventMapperProviderC);
        $this->providerAggregator = new AggregatorProvider();
    }


    public function testSubscribeProvider()
    {
        $eventA = new EventSampleA();
        $this->providerAggregator->subscribeProvider($this->delegatingProviderTypeA);
        /** @var \Traversable $listenersBefore */
        $listenersBefore = $this->providerAggregator->getListenersForEvent($eventA);
        $this->assertEquals(3, iterator_count($listenersBefore));

        $this->providerAggregator->subscribeProvider($this->delegatingProviderTypeB);
        /** @var \Traversable $listenersAfter */
        $listenersAfter = $this->providerAggregator->getListenersForEvent($eventA);
        $this->assertEquals(5, iterator_count($listenersAfter));
    }

    public function testUnsubscribeProvider()
    {
        $eventA = new EventSampleA();
        $this->providerAggregator->subscribeProvider($this->delegatingProviderTypeB);
        /** @var \Traversable $listenersBefore */
        $listenersBefore = $this->providerAggregator->getListenersForEvent($eventA);
        $this->assertEquals(2, iterator_count($listenersBefore));
        $this->providerAggregator->unsubscribeProvider($this->delegatingProviderTypeB);
        /** @var \Traversable $listenersAfter */
        $listenersAfter = $this->providerAggregator->getListenersForEvent($eventA);
        $this->assertEquals(0, iterator_count($listenersAfter));
    }
}
