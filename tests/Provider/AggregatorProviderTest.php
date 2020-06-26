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
use Pinoven\Dispatcher\Samples\EventSampleB;
use Pinoven\Dispatcher\Samples\ListenerSampleA;
use Pinoven\Dispatcher\Samples\ListenerSampleB;
use Pinoven\Dispatcher\Samples\ListenerSampleE;

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

    public function testProviderListenersWithPriority()
    {
        $eventB = new EventSampleB();
        $mapper1 = new class($this->proxy) extends EventMapperProviderSampleDefault{
            public function getEventType(): string
            {
                return EventSampleB::class;
            }
            public function mapListeners(): iterable
            {
                return [
                    new class extends ListenerSampleE {
                        public $level = 1;
                        public $priority = 3;
                    }
                ];
            }
        };
        $mapper2 = new class($this->proxy) extends EventMapperProviderSampleDefault{
            public function getEventType(): string
            {
                return EventSampleB::class;
            }
            public function mapListeners(): iterable
            {
                return [
                    new class extends ListenerSampleE {
                        public $level = 2;
                        public $priority = 1;
                    },
                    new class extends ListenerSampleE {
                        public $level = 3;
                        public $priority = 5;
                    }
                ];
            }
        };
        $mapper3 = new class($this->proxy) extends EventMapperProviderSampleDefault{
            public function getEventType(): string
            {
                return EventSampleB::class;
            }
            public function mapListeners(): iterable
            {
                return [
                    new class extends ListenerSampleE {
                        public $level = 4;
                        public $priority = 1;
                    },
                    new class extends ListenerSampleE {
                        public $level = 5;
                        public $priority = 4;
                    }
                ];
            }
        };
        $mapper4 = new class($this->proxy) extends EventMapperProviderSampleDefault{
            public function getEventType(): string
            {
                return EventSampleB::class;
            }
            public function mapListeners(): iterable
            {
                return [
                    new class extends ListenerSampleE {
                        public $level = 6;
                        public $priority = 6;
                    }
                ];
            }
        };
        $delegatingProvider = new DelegatingProvider();
        $delegatingProvider2 = new DelegatingProvider();
        $delegatingProvider->subscribe($mapper1);
        $delegatingProvider->subscribe($mapper2);
        $delegatingProvider2->subscribe($mapper3);
        $delegatingProvider2->subscribe($mapper4);
        $prioritize = new PrioritizeProvider();
        $providerAggregator = new AggregatorProvider($prioritize);
        $providerAggregator->subscribeProvider($delegatingProvider);
        $providerAggregator->subscribeProvider($delegatingProvider2);
        /** @var \Traversable $listenersGen */
        $listenerItems = $providerAggregator->getListenersForEvent($eventB);
        /** @var ListenerSampleB[] $items */
        $items = [];
        foreach ($listenerItems as $listenerItem) {
            $items[] = $listenerItem;
        }
        $this->assertEquals(1, $items[0]->level);
        $this->assertEquals(2, $items[1]->level);
        $this->assertEquals(3, $items[2]->level);
        $this->assertEquals(4, $items[3]->level);
        $this->assertEquals(5, $items[4]->level);
        $this->assertEquals(6, $items[5]->level);
        $providerAggregator1 = new AggregatorProvider($prioritize, true);
        $providerAggregator1->subscribeProvider($delegatingProvider);
        $providerAggregator1->subscribeProvider($delegatingProvider2);
        /** @var \Traversable $listenersGen */
        $listenerItems = $providerAggregator1->getListenersForEvent($eventB);
        /** @var ListenerSampleB[] $items */
        $items = [];
        foreach ($listenerItems as $listenerItem) {
            $items[] = $listenerItem;
        }
        $this->assertEquals(6, $items[0]->level);
        $this->assertEquals(3, $items[1]->level);
        $this->assertEquals(5, $items[2]->level);
        $this->assertEquals(1, $items[3]->level);
        $this->assertEquals(2, $items[4]->level);
        $this->assertEquals(4, $items[5]->level);
    }
}
