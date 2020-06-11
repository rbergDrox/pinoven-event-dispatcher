<?php


namespace Pinoven\Dispatcher\Dispatch;

use PHPUnit\Framework\TestCase;
use Pimple\Container as PimpleContainer;
use Pimple\Psr11\Container;
use Pinoven\Dispatcher\Listener\ProxyCallableListenersMapper;
use Pinoven\Dispatcher\Provider\DelegatingType;
use Pinoven\Dispatcher\Provider\ProviderAggregatorProvider;
use Pinoven\Dispatcher\Samples\EventMapperProviderSampleB;
use Pinoven\Dispatcher\Samples\EventMapperProviderSampleC;
use Pinoven\Dispatcher\Samples\EventMapperProviderSampleD;
use Pinoven\Dispatcher\Samples\EventMapperProviderSampleDefault;
use Pinoven\Dispatcher\Samples\EventMapperProviderSampleE;
use Pinoven\Dispatcher\Samples\EventSampleA;
use Pinoven\Dispatcher\Samples\EventSampleB;
use Pinoven\Dispatcher\Samples\ListenerSampleA;

/**
 * Class DispatcherTest
 * @package Pinoven\Dispatcher\Dispatch
 */
class DispatcherTest extends TestCase
{

    /**
     * @var ProviderAggregatorProvider
     */
    private $providerAggregatorB;
    /**
     * @var Dispatcher
     */
    private $dispatcher;
    /**
     * @var ProxyCallableListenersMapper
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
        $this->proxy = new ProxyCallableListenersMapper($container);
        $eventMapperProviderB = new EventMapperProviderSampleB($this->proxy);
        $eventMapperProviderC = new EventMapperProviderSampleC($this->proxy);
        $eventMapperDefault = new EventMapperProviderSampleDefault($this->proxy);
        $delegatingTypeA = new DelegatingType($eventMapperDefault);
        $delegatingTypeA->subscribeEventTypeMapper($eventMapperProviderB);
        $delegatingTypeB = new DelegatingType($eventMapperDefault);
        $delegatingTypeB->subscribeEventTypeMapper($eventMapperProviderC);
        $providerAggregatorA = new ProviderAggregatorProvider();
        $this->providerAggregatorB = new ProviderAggregatorProvider();
        $this->providerAggregatorB->subscribeProvider($delegatingTypeB);
        $providerAggregatorA->subscribeProvider($delegatingTypeA);
        $this->dispatcher = new Dispatcher($providerAggregatorA);
    }

    public function testDispatch()
    {
        $eventA = new EventSampleA();
        $event = $this->dispatcher->dispatch($eventA);
        $this->assertEquals($eventA, $event);
        $this->assertEquals(2, $eventA->getIncrement());
        $this->assertEquals(2, $event->getIncrement());
    }

    public function testDispatcherAfterSetAggregator()
    {
        $eventA = new EventSampleA();
        $this->dispatcher->setAggregator($this->providerAggregatorB);
        $event = $this->dispatcher->dispatch($eventA);
        $this->assertEquals($eventA, $event);
        $this->assertEquals(1, $eventA->getIncrement());
        $this->assertEquals(1, $event->getIncrement());
    }


    public function testDispatcherWithEventStoppable()
    {
        $eventB = new EventSampleB();
        $delegateProvider = new DelegatingType();
        $delegateProvider->subscribeEventTypeMapper(new EventMapperProviderSampleD($this->proxy));
        $delegateProvider->subscribeEventTypeMapper(new EventMapperProviderSampleE($this->proxy));
        $providerAggregator = new ProviderAggregatorProvider();
        $providerAggregator->subscribeProvider($delegateProvider);
        $dispatcher = new Dispatcher($providerAggregator);
        $this->assertFalse($eventB->isPropagationStopped());
        $event = $dispatcher->dispatch($eventB);
        $this->assertTrue($eventB->isPropagationStopped());
        $this->assertTrue($event->isPropagationStopped());
        $this->assertEquals(2, $eventB->getIncrement());
        $this->assertEquals(2, $event->getIncrement());
    }
}
