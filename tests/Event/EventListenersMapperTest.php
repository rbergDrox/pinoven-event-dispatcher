<?php


namespace Pinoven\Dispatcher\Event;

use PHPUnit\Framework\TestCase;
use Pimple\Container as PimpleContainer;
use Pimple\Psr11\Container;
use Pinoven\Dispatcher\Listener\ProxyListeners;
use Pinoven\Dispatcher\Samples\EventMapperProviderSample;
use Pinoven\Dispatcher\Samples\EventSampleA;
use Pinoven\Dispatcher\Samples\EventSampleB;
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
    /**
     * @var EventListenersMapper
     */
    private $customEventMapperProvider;

    public function setUp(): void
    {
        $container = new Container(new PimpleContainer([
            'eventListenerInvoked' =>  new ListenerSampleA(),
            ListenerSampleA::class => new ListenerSampleA()
        ]));
        $proxy = new ProxyListeners($container);
        $this->eventMapperProvider = new EventMapperProviderSample($proxy);
        $this->customEventMapperProvider = new class($proxy) extends EventListenersMapper {

            public function mapListeners(): iterable
            {
                return [
                    new class {
                        public function sendDataEmailHandler(CustomEvent $event)
                        {
                            $event->test = 0;
                        }
                    },
                    new class {
                        public function sendDataEmailHandler(EventSampleB $event)
                        {
                            $event->test = 0;
                        }
                    },
                    new class {
                        public function sendDataEmailHandler(CustomEvent $event)
                        {
                            $event->test = 0;
                        }
                    },
                ];
            }

            public function getEventType(): string
            {
                return 'send.data.email';
            }
        };
    }

    public function testGetListenersForEvent()
    {
        $eventA = new EventSampleA();
        /** @var \Traversable $listeners */
        $listeners = $this->eventMapperProvider->getListenersForEvent($eventA);
        $this->assertEquals(3, iterator_count($listeners));
    }

    public function testGetListenersForCustomEvent()
    {
        $customEvent = new CustomEvent('send.data.email');
        /** @var \Traversable $listeners */
        $listeners = $this->customEventMapperProvider->getListenersForEvent($customEvent);
        $this->assertEquals(2, iterator_count($listeners));
    }
}
