<?php


namespace Pinoven\Dispatcher\Provider;

use PHPUnit\Framework\TestCase;
use Pimple\Container as PimpleContainer;
use Pimple\Psr11\Container;
use Pinoven\Dispatcher\Listener\ProxyListenersMapper;
use Pinoven\Dispatcher\Priority\PrioritizeProvider;
use Pinoven\Dispatcher\Samples\EventMapperProviderSampleB;
use Pinoven\Dispatcher\Samples\EventMapperProviderSampleC;
use Pinoven\Dispatcher\Samples\EventMapperProviderSampleDefault;
use Pinoven\Dispatcher\Samples\EventSampleA;
use Pinoven\Dispatcher\Samples\ListenerSampleA;
use Pinoven\Dispatcher\Samples\ListenerSampleB;

class AggregatorProviderTest extends TestCase
{

    /**
     * @var DelegatingProvider
     */
    private $delegatingProviderA;

    /**
     * @var DelegatingProvider
     */
    private $delegatingProviderB;

    /**
     * @var AggregatorProvider
     */
    private $providerAggregator;

    /**
     * @var ProxyListenersMapper
     */
    private $proxy;

    public function setUp(): void
    {
        $container = new Container(new PimpleContainer([
            'eventListenerInvoked' =>  new class {
                public function handler(EventSampleA $eventSampleA)
                {
                    $eventSampleA->increment();
                }
            },
            ListenerSampleA::class => new ListenerSampleA()
        ]));
        $this->proxy = new ProxyListenersMapper($container);
        $eventMapperProviderB = new EventMapperProviderSampleB($this->proxy);
        $eventMapperProviderC = new EventMapperProviderSampleC($this->proxy);
        $eventMapperDefault = new EventMapperProviderSampleDefault($this->proxy);
        $this->delegatingProviderA = new DelegatingProvider($eventMapperDefault);
        $this->delegatingProviderA->subscribe($eventMapperProviderB);
        $this->delegatingProviderB = new DelegatingProvider($eventMapperDefault);
        $this->delegatingProviderB->subscribe($eventMapperProviderC);
        $this->providerAggregator = new AggregatorProvider();
    }


    public function testSubscribeProvider()
    {
        $eventA = new EventSampleA();
        $this->providerAggregator->subscribeProvider($this->delegatingProviderA);
        /** @var \Traversable $listenersBefore */
        $listenersBefore = $this->providerAggregator->getListenersForEvent($eventA);
        $this->assertEquals(3, iterator_count($listenersBefore));

        $this->providerAggregator->subscribeProvider($this->delegatingProviderB);
        /** @var \Traversable $listenersAfter */
        $listenersAfter = $this->providerAggregator->getListenersForEvent($eventA);
        $this->assertEquals(5, iterator_count($listenersAfter));
    }

    public function testUnsubscribeProvider()
    {
        $eventA = new EventSampleA();
        $this->providerAggregator->subscribeProvider($this->delegatingProviderB);
        /** @var \Traversable $listenersBefore */
        $listenersBefore = $this->providerAggregator->getListenersForEvent($eventA);
        $this->assertEquals(2, iterator_count($listenersBefore));
        $this->providerAggregator->unsubscribeProvider($this->delegatingProviderB);
        /** @var \Traversable $listenersAfter */
        $listenersAfter = $this->providerAggregator->getListenersForEvent($eventA);
        $this->assertEquals(0, iterator_count($listenersAfter));
    }

    public function testProviderWithPriority()
    {
        $eventA= new EventSampleA();
        $mapper1 = new class($this->proxy) extends EventMapperProviderSampleDefault{
            public function getEventType(): string
            {
                return EventSampleA::class;
            }
            public function mapListeners(): iterable
            {
                return [
                    new class extends ListenerSampleB{
                        public $level = 1;
                        public function handler(EventSampleA $eventSampleA)
                        {
                            $eventSampleA->increment();
                        }
                    }
                ];
            }
        };
        $mapper2 = new class($this->proxy) extends EventMapperProviderSampleDefault{
            public function getEventType(): string
            {
                return EventSampleA::class;
            }
            public function mapListeners(): iterable
            {
                return [
                    new class extends ListenerSampleB{
                        public $level = 2;
                        public function handler(EventSampleA $eventSampleA)
                        {
                            $eventSampleA->increment();
                        }
                    }
                ];
            }
        };
        $mapper3 = new class($this->proxy) extends EventMapperProviderSampleDefault{
            public function getEventType(): string
            {
                return EventSampleA::class;
            }
            public function mapListeners(): iterable
            {
                return [
                new class extends ListenerSampleB{
                    public $level = 4;
                    public function handler(EventSampleA $eventSampleA)
                    {
                        $eventSampleA->increment();
                    }
                }
                ];
            }
        };
        $mapper4 = new class($this->proxy) extends EventMapperProviderSampleDefault{
            public function getEventType(): string
            {
                return EventSampleA::class;
            }
            public function mapListeners(): iterable
            {
                return [
                    new class extends ListenerSampleB{
                        public $level = 3;
                        public function handler(EventSampleA $eventSampleA)
                        {
                            $eventSampleA->increment();
                        }
                    }
                ];
            }
        };
        $delegatingProvider = new DelegatingProvider();
        $delegatingProvider2 = new DelegatingProvider();
        $delegatingProvider->subscribe($mapper1);
        $delegatingProvider->subscribe($mapper2);
        $delegatingProvider->setPriority(3);
        $delegatingProvider2->subscribe($mapper3);
        $delegatingProvider2->subscribe($mapper4);
        $delegatingProvider2->setPriority(9);
        $prioritize = new PrioritizeProvider();
        $providerAggregator = new AggregatorProvider($prioritize);
        $providerAggregator->subscribeProvider($delegatingProvider);
        $providerAggregator->subscribeProvider($delegatingProvider2);
        /** @var \Traversable $listenersGen */
        $listenerItems = $providerAggregator->getListenersForEvent($eventA);
        /** @var ListenerSampleB[] $items */
        $items = [];
        foreach ($listenerItems as $listenerItem) {
            $items[] = $listenerItem[0];
        }
        $this->assertEquals(4, $items[0]->level);
        $this->assertEquals(3, $items[1]->level);
        $this->assertEquals(1, $items[2]->level);
        $this->assertEquals(2, $items[3]->level);
    }
}
