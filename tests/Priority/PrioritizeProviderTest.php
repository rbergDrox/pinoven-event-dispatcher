<?php


namespace Pinoven\Dispatcher\Priority;

use PHPUnit\Framework\TestCase;
use Pimple\Container as PimpleContainer;
use Pimple\Psr11\Container;
use Pinoven\Dispatcher\Listener\ProxyListenersMapper;
use Pinoven\Dispatcher\Samples\EventMapperProviderSample;
use Pinoven\Dispatcher\Samples\EventMapperProviderSampleB;
use Pinoven\Dispatcher\Samples\EventMapperProviderSampleC;
use Pinoven\Dispatcher\Samples\EventMapperProviderSampleDefault;
use Pinoven\Dispatcher\Samples\EventMapperProviderSampleE;
use Pinoven\Dispatcher\Samples\EventSampleA;
use Pinoven\Dispatcher\Samples\ListenerSampleA;

/**
 * Class PrioritizeTest
 * @package Pinoven\Dispatcher\Priority
 */
class PrioritizeProviderTest extends TestCase
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
        $proxy = new ProxyListenersMapper($container);
        $providers = [
            new EventMapperProviderSampleDefault($proxy),//0
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
        $prioritizeProvider = new PrioritizeProvider();
        $sortedProviders= $prioritizeProvider->sortItems($providers);
        $sortedClassList = $provideClasses($sortedProviders);
        $this->assertFalse($sortedClassList === $classList);
        $this->assertEquals([
            EventMapperProviderSampleB::class,
            EventMapperProviderSampleE::class,
            EventMapperProviderSampleDefault::class,
            EventMapperProviderSample::class,
            EventMapperProviderSampleC::class,
        ], $sortedClassList);
    }
}
