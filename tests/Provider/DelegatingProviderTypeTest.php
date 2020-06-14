<?php


namespace Pinoven\Dispatcher\Provider;

use PHPUnit\Framework\TestCase;
use Pimple\Container as PimpleContainer;
use Pimple\Psr11\Container;
use Pinoven\Dispatcher\Listener\ProxyListenersMapper;
use Pinoven\Dispatcher\Priority\PrioritizeProvider;
use Pinoven\Dispatcher\Samples\EventMapperProviderSampleB;
use Pinoven\Dispatcher\Samples\EventMapperProviderSampleDefault;
use Pinoven\Dispatcher\Samples\EventSampleA;
use Pinoven\Dispatcher\Samples\EventSampleB;
use Pinoven\Dispatcher\Samples\ListenerSampleA;
use Pinoven\Dispatcher\Samples\ListenerSampleB;

class DelegatingProviderTypeTest extends TestCase
{

    /**
     * @var EventMapperProviderSampleB
     */
    private $eventMapperProviderB;

    /**
     * @var DelegatingProvider
     */
    private $delegatingProvider;

    /**
     * @var EventMapperProviderSampleDefault
     */
    private $eventMapperDefault;

    /**
     * @var ProxyListenersMapper
     */
    private $proxy;

    public function setUp(): void
    {
        $container = new Container(new PimpleContainer([
            'eventListenerInvoked' =>  function () {
                    return function (EventSampleA $eventSample) {
                        $eventSample->increment();
                    };
            },
            ListenerSampleA::class => new ListenerSampleA()
        ]));
        $this->proxy = new ProxyListenersMapper($container);
        $this->eventMapperProviderB = new EventMapperProviderSampleB($this->proxy);
        $this->eventMapperDefault = new EventMapperProviderSampleDefault($this->proxy);
        $this->delegatingProvider = new DelegatingProvider($this->eventMapperDefault);
    }

    public function testSubscribeEventTypeMapper()
    {
        $eventB = new EventSampleB();
        /** @var \Traversable $listenersBefore */
        $listenersBefore = $this->delegatingProvider->getListenersForEvent($eventB);
        $this->assertEquals(0, iterator_count($listenersBefore));
        $this->delegatingProvider->subscribe($this->eventMapperProviderB);
        $listenersAfter = $this->delegatingProvider->getListenersForEvent($eventB);
        /** @var \Traversable $listenersAfter */
        $this->assertEquals(2, iterator_count($listenersAfter));
    }

    public function testUnsubscribeEventTypeMapper()
    {
        $eventB = new EventSampleB();
        $this->delegatingProvider->subscribe($this->eventMapperProviderB);
        /** @var \Traversable $listenersBefore */
        $listenersBefore = $this->delegatingProvider->getListenersForEvent($eventB);
        $this->assertEquals(2, iterator_count($listenersBefore));
        $this->delegatingProvider->unsubscribe($this->eventMapperProviderB);
        /** @var \Traversable $listenersAfter */
        $listenersAfter = $this->delegatingProvider->getListenersForEvent($eventB);
        $this->assertEquals(0, iterator_count($listenersAfter));
    }

    public function testEventTypeMapperWithPriority()
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
        $prioritize = new PrioritizeProvider();
        $delegatingProvider = new DelegatingProvider($this->eventMapperDefault, $prioritize);
        $delegatingProvider->subscribe($mapper1);
        $delegatingProvider->subscribe($mapper2);
        $mapper2->setPriority(4);
        /** @var \Traversable $listenersGen */
        $listenerItems = $delegatingProvider->getListenersForEvent($eventA);
        /** @var ListenerSampleB[] $items */
        $items = [];
        foreach ($listenerItems as $listenerItem) {
            $items[] = $listenerItem[0];
        }
        $this->assertEquals(2, $items[0]->level);
        $this->assertEquals(1, $items[1]->level);
    }
}
