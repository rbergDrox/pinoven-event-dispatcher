<?php


namespace Pinoven\Dispatcher\Provider;

use PHPUnit\Framework\TestCase;
use Pimple\Container as PimpleContainer;
use Pimple\Psr11\Container;
use Pinoven\Dispatcher\Listener\ProxyListenersMapper;
use Pinoven\Dispatcher\Samples\EventMapperProviderSampleB;
use Pinoven\Dispatcher\Samples\EventMapperProviderSampleDefault;
use Pinoven\Dispatcher\Samples\EventSampleA;
use Pinoven\Dispatcher\Samples\EventSampleB;
use Pinoven\Dispatcher\Samples\ListenerSampleA;

class DelegatingProviderTypeTest extends TestCase
{

    /**
     * @var EventMapperProviderSampleB
     */
    private $eventMapperProviderB;
    /**
     * @var DelegatingProvider
     */
    private $delegatingProviderType;

    public function setUp(): void
    {
        $container = new Container(new PimpleContainer([
            'eventListenerInvoked' =>  function () {
                    return function (EventSampleA $eventSample) {
                    };
            },
            ListenerSampleA::class => new ListenerSampleA()
        ]));
        $proxy = new ProxyListenersMapper($container);
        $this->eventMapperProviderB = new EventMapperProviderSampleB($proxy);
        $eventMapperDefault = new EventMapperProviderSampleDefault($proxy);
        $this->delegatingProviderType = new DelegatingProvider($eventMapperDefault);
    }

    public function testSubscribeEventTypeMapper()
    {
        $eventB = new EventSampleB();
        /** @var \Traversable $listenersBefore */
        $listenersBefore = $this->delegatingProviderType->getListenersForEvent($eventB);
        $this->assertEquals(0, iterator_count($listenersBefore));
        $this->delegatingProviderType->subscribe($this->eventMapperProviderB);
        $listenersAfter = $this->delegatingProviderType->getListenersForEvent($eventB);
        /** @var \Traversable $listenersAfter */
        $this->assertEquals(2, iterator_count($listenersAfter));
    }

    public function testUnsubscribeEventTypeMapper()
    {
        $eventB = new EventSampleB();
        $this->delegatingProviderType->subscribe($this->eventMapperProviderB);
        /** @var \Traversable $listenersBefore */
        $listenersBefore = $this->delegatingProviderType->getListenersForEvent($eventB);
        $this->assertEquals(2, iterator_count($listenersBefore));
        $this->delegatingProviderType->unsubscribe($this->eventMapperProviderB);
        /** @var \Traversable $listenersAfter */
        $listenersAfter = $this->delegatingProviderType->getListenersForEvent($eventB);
        $this->assertEquals(0, iterator_count($listenersAfter));
    }
}
