<?php


namespace Pinoven\Dispatcher\Listener;

use DateInterval;
use DateTime;
use PHPUnit\Framework\TestCase;
use Pimple\Container as PimpleContainer;
use Pimple\Psr11\Container;
use Pinoven\Dispatcher\Priority\ItemPriorityInterface;
use Pinoven\Dispatcher\Priority\CallableItemPriorityFactory;
use Pinoven\Dispatcher\Samples\EventSampleB;
use Pinoven\Dispatcher\Samples\ListenerSampleD;
use stdClass;

class ProxyListenersTest extends TestCase
{
    /** @var ProxyListener */
    private $proxy;

    public function setUp(): void
    {
        $this->proxy = new ProxyListeners();
        $container = new Container(new PimpleContainer([
            'container1' => new DateTime(),
            'container2' => [DateInterval::class, 'createFromDateString'],
            'container3' => function () {
                return function () {
                    echo 'test';
                };
            },
            'container5' => 5,
            'container6' => function () {
                return function (DateTime $event) {
                    echo $event->format('Y');
                };
            },
        ]));
        $this->proxy->setContainer($container);
    }

    /**
     * @throws \ReflectionException
     */
    public function testListenerToCallableGetCallableWithClosure()
    {
        $listener = function (stdClass $event) {
            $event->test = 0;
        };
        $value = $this->proxy->listenerToCallable('testEvent', $listener, '__invoke');
        $this->assertIsCallable($value);
    }

    /**
     * @throws \ReflectionException
     */
    public function testListenerToCallableGetCallableWithContainerNotSet()
    {
        $proxy = new ProxyListeners();
        $value = $proxy->listenerToCallable('testEvent', 'container4', 'format');
        $this->assertNull($value);
    }

    /**
     * @throws \ReflectionException
     */
    public function testListenerToCallableGetCallableWithContainer()
    {
        $value = $this->proxy->listenerToCallable('testEvent', 'container1', 'add');
        $this->assertIsCallable($value);
        $value2 = $this->proxy->listenerToCallable('testEvent', 'container2', 'createFromDateString');
        $this->assertIsCallable($value2);
        $value3 = $this->proxy->listenerToCallable('testEvent', 'container3', 'format');
        $this->assertIsCallable($value3);
    }

    /**
     * @throws \ReflectionException
     */
    public function testListenerToCallableGetCallableWithContainerGotNull()
    {
        $value = $this->proxy->listenerToCallable('testEvent', 'container4', 'format');
        $this->assertNull($value);
        $value2 = $this->proxy->listenerToCallable('testEvent', 'container5', 'format');
        $this->assertNull($value2);
    }

    /**
     * @throws \ReflectionException
0     */
    public function testListenerToCallableGetCallableWithObjectInvokable()
    {
        $listener = new class {
            public function __invoke(stdClass $event)
            {
                $event->test = 0;
            }
        };
        $value = $this->proxy->listenerToCallable('testEvent', $listener, '__invoke');
        $this->assertIsCallable($value);
        $value2 = $this->proxy->listenerToCallable('testEvent', new class {
        }, 'format');
        $this->assertNull($value2);
    }

    /**
     * @throws \ReflectionException
     */
    public function testListenerToCallableGetCallableObjectNonInvokable()
    {
        $value = $this->proxy->listenerToCallable('testEvent', new DateTime(), 'add');
        $this->assertIsCallable($value);
        $value2 = $this->proxy->listenerToCallable('testEvent', DateInterval::class, 'createFromDateString');
        $this->assertIsCallable($value2);
    }

    /**
     * @throws \ReflectionException
     */
    public function testListenerToCallableGetCallableObjectNonInvokableGotNull()
    {
        $value = $this->proxy->listenerToCallable('testEvent', DateInterval::class, 'testEvent');
        $this->assertNull($value);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetCallableListeners()
    {
        /** @var \Traversable $value */
        $value = $this->proxy->getCallableListeners(DateTime::class, [ new class {
            public function add(DateTime $event)
            {
                $event->add(new DateInterval('1PT'));
            }
        }, 'container6'], 'add');
        $this->assertEquals(2, iterator_count($value));
    }

    /**
     * @throws \ReflectionException
     */
    public function testListenerToCallableGetCallableGetWrapItemPriorityInterface()
    {
        $test1 = new class {
            public function __invoke(EventSampleB $eventSampleA)
            {
                $eventSampleA->increment();
            }
        };
        $test2 =new ListenerSampleD();
        $proxy = new ProxyListeners();
        /** @var \Traversable $listeners */
        $listeners = $proxy->getCallableListeners(EventSampleB::class, [ $test1, $test2], '__invoke');
        $listenersArray = iterator_to_array($listeners);
        $this->assertNotNull($listenersArray[0]);
        $this->assertNotNull($listenersArray[1]);
        $this->assertNotInstanceOf(ItemPriorityInterface::class, $listenersArray[0]);
        $this->assertInstanceOf(ItemPriorityInterface::class, $listenersArray[1]);
        $wrapFactory = new CallableItemPriorityFactory();
        $proxy1 = new ProxyListeners(null, $wrapFactory);
        $listeners = $proxy->getCallableListeners(EventSampleB::class, [ $test1, $test2], '__invoke');
        $listenersArray = iterator_to_array($listeners);
        $this->assertNotNull($listenersArray[0]);
        $this->assertNotNull($listenersArray[1]);
        $this->assertNotInstanceOf(ItemPriorityInterface::class, $listenersArray[0]);
        $this->assertInstanceOf(ItemPriorityInterface::class, $listenersArray[1]);
        /** @var \Traversable $listeners */
        $listeners = $proxy1->getCallableListeners(EventSampleB::class, [ $test1, $test2], '__invoke');
        $listenersArray = iterator_to_array($listeners);
        $this->assertNotNull($listenersArray[0]);
        $this->assertNotNull($listenersArray[1]);
        $this->assertInstanceOf(ItemPriorityInterface::class, $listenersArray[0]);
        $this->assertInstanceOf(ItemPriorityInterface::class, $listenersArray[1]);
    }
}
