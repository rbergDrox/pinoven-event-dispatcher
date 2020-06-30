<?php


namespace Pinoven\Dispatcher\Event;

use PHPUnit\Framework\TestCase;
use Pimple\Container as PimpleContainer;
use Pimple\Psr11\Container;
use Pinoven\Dispatcher\Listener\ProxyListeners;
use Pinoven\Dispatcher\Samples\EventMapperProviderSample;
use Pinoven\Dispatcher\Samples\EventSampleA;
use Pinoven\Dispatcher\Samples\ListenerSampleA;

/**
 * Class EventListenersMapperTest
 * @package Pinoven\Dispatcher\Event
 *
 */
class EventListenersMapperTest extends TestCase
{

    /**
     * @var EventMapperProviderSample
     */
    private $eventMapperProvider;

    public function setUp(): void
    {
        $container = new Container(new PimpleContainer([
            'eventListenerInvoked' =>  new ListenerSampleA(),
            ListenerSampleA::class => new ListenerSampleA()
        ]));
        $proxy = new ProxyListeners($container);
        $this->eventMapperProvider = new EventMapperProviderSample($proxy);
    }

    public function testGetListenersForEvent()
    {
        $eventA = new EventSampleA();
        /** @var \Traversable $listeners */
        $listeners = $this->eventMapperProvider->getListenersForEvent($eventA);
        $this->assertEquals(3, iterator_count($listeners));
    }
}