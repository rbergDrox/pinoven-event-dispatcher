<?php


namespace Pinoven\Dispatcher\Priority;

use PHPUnit\Framework\TestCase;
use Pimple\Container as PimpleContainer;
use Pimple\Psr11\Container;
use Pinoven\Dispatcher\Listener\ProxyListeners;
use Pinoven\Dispatcher\Samples\EventMapperProviderSample;
use Pinoven\Dispatcher\Samples\EventMapperProviderSampleB;
use Pinoven\Dispatcher\Samples\EventMapperProviderSampleC;
use Pinoven\Dispatcher\Samples\EventListenersMapperSampleDefault;
use Pinoven\Dispatcher\Samples\EventMapperProviderSampleE;
use Pinoven\Dispatcher\Samples\EventSampleA;
use Pinoven\Dispatcher\Samples\ListenerSampleA;

/**
 * Class PrioritizeTest
 * @package Pinoven\Dispatcher\Priority
 */
class PrioritizeTest extends TestCase
{

    public function testSortItems()
    {
        $container = new Container(new PimpleContainer([
            'eventListenerInvoked' =>  new class {
                public function handler(EventSampleA $eventSampleA)
                {
                }
            },
            ListenerSampleA::class => new ListenerSampleA()
        ]));
        $proxy = new ProxyListeners($container);
        $providers = [
            new EventListenersMapperSampleDefault($proxy),//0
            new EventMapperProviderSampleB($proxy),//10
            new EventMapperProviderSampleC($proxy),//-2
            new EventMapperProviderSample($proxy),//0
            new EventMapperProviderSampleE($proxy),//7
        ];

        /**
         * @param array $providers
         * @return string[]
         */
        $provideClasses = function (array $providers) {
            return array_filter(array_map(function (ItemPriorityInterface $provider) {
                return get_class($provider);
            }, $providers), function ($className) {
                return $className;
            });
        };
        $classList = $provideClasses($providers);
        $prioritizeProvider = new Prioritize();
        $sortedProviders= $prioritizeProvider->sortItems($providers);
        $sortedClassList = $provideClasses($sortedProviders);
        $this->assertFalse($sortedClassList === $classList);
        $this->assertEquals([
            EventMapperProviderSampleB::class,
            EventMapperProviderSampleE::class,
            EventListenersMapperSampleDefault::class,
            EventMapperProviderSample::class,
            EventMapperProviderSampleC::class,
        ], $sortedClassList);
    }

    public function testSorIteratorCallableItems()
    {
        $item0 = new class {
            public $test = 0;
            public function __invoke(EventSampleA $eventSampleA)
            {
                $eventSampleA->increment();
            }
        };
        $item1 = clone $item0;
        $item1->test = 1;
        $item2 = new class implements ItemPriorityInterface{
            public $priority = 0;
            public $test = 2;
            /**
             * @inheritDoc
             */
            public function getPriority(): int
            {
                return $this->priority;
            }

            /**
             * @inheritDoc
             */
            public function setPriority(int $priority): void
            {
                $this->priority = $priority;
            }
        };
        $item3 = clone $item2;
        $item3->priority = 4;
        $item3->test = 3;
        $item4 = clone $item0;
        $item4->test = 4;

        $iteratorCallable = function (array $items) {
            yield from $items;
        };
        $listeners = $iteratorCallable([$item1, $item2]);
        $prioritizeProvider = new Prioritize();
        $sortedListeners = $prioritizeProvider->sortItems($listeners);
        $this->assertEquals(1, $sortedListeners[0]->test);
        $this->assertEquals(2, $sortedListeners[1]->test);
        $listeners2 = $iteratorCallable([$item0, $item1, $item2, $item3, $item4]);
        $sortedListeners2 = $prioritizeProvider->sortItems($listeners2);
        $this->assertEquals(3, $sortedListeners2[0]->test);
        $this->assertEquals(0, $sortedListeners2[1]->test);
        $this->assertEquals(1, $sortedListeners2[2]->test);
        $this->assertEquals(2, $sortedListeners2[3]->test);
        $this->assertEquals(4, $sortedListeners2[4]->test);
    }
}
