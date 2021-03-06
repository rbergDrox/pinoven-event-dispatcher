# Pinoven: Event Dispatcher  [![Pinoven](https://circleci.com/gh/rbergDrox/pinoven-event-dispatcher.svg?style=svg)](https://circleci.com/gh/rbergDrox/pinoven-event-dispatcher/tree/master)

[PSR-14: Event Dispatcher](https://github.com/php-fig/event-dispatcher) compatible.
Implement event dispatcher to dispatch events. It provides a way to register listeners and subscribe/unsubscribe many listeners providers.
Dispatcher can retrieve listeners through different providers by using an aggregator.

# Features/Usage

## Dispatcher
```php
    use Pinoven\Dispatcher\Dispatch\EventDispatcher;
    use Pinoven\Dispatcher\Provider\AggregatorProvider;
    use Pinoven\Dispatcher\Samples\EventSampleA;
 
    $aggregator = new AggregatorProvider();
    $eventDispatcher =  new EventDispatcher($aggregator);
    $event = new EventSampleA();
    $eventDispatcher->dispatch($event);
```

## Aggregator, Delegate, Mapper Setup

```php
    use Fig\EventDispatcher\DelegatingProvider as FigDelegatingProvider;
    use Pinoven\Dispatcher\Listener\ProxyListeners;
    use Pinoven\Dispatcher\Provider\AggregatorProvider;
    use Pinoven\Dispatcher\Provider\DelegatingProvider;
    use Pinoven\Dispatcher\Samples\EventMapperProviderSample;
    use Pinoven\Dispatcher\Samples\EventMapperProviderSampleB;
    use Pinoven\Dispatcher\Samples\EventMapperProviderSampleC;
    use Pinoven\Dispatcher\Samples\EventSampleA;
 
    $aggregator = new AggregatorProvider();
    $proxy = new ProxyListeners();
    $defaultListenerProvider = new FigDelegatingProvider();
    $eventMapper1 = new  EventMapperProviderSample($proxy);
    $eventMapper2 = new  EventMapperProviderSampleB($proxy);
    $eventMapper3 = new  EventMapperProviderSampleC($proxy);
    $defaultListenerProvider->addProvider($eventMapper3, [EventSampleA::class]);
    $delegateListenerProvider1 = new DelegatingProvider();
    $delegateListenerProvider1->subscribe($eventMapper1);
    $delegateListenerProvider1->subscribe($eventMapper2);
    // \Pinoven\Dispatcher\Provider\DelegatingType() Can take a default listenerProvider.
    $delegateListenerProvider2 = new DelegatingProvider($defaultListenerProvider);
    $aggregator->addProvider($delegateListenerProvider1);
    $aggregator->addProvider($delegateListenerProvider2);
```
## Mapper for Event/Listeners

```php
use Pinoven\Dispatcher\Event\EventListenersMapper;
EventListenersMapper::class;
```

It helps to declare the event type and related listeners.

```php
use Pinoven\Dispatcher\Event\EventListenersMapper;
EventListenersMapper::class;
```
## Emit Event

```php
use Pinoven\Dispatcher\Event\EventEmitter;
EventEmitter::class;
```

Emitter event permit to use the dispatcher and send a custom event too by using string and payload.


## Order/Prioritize

### Order items
To manage sorting on providers, listeners you have to implement:

```php
use Pinoven\Dispatcher\Priority\PrioritizeInterface;
class Prioritize implements PrioritizeInterface 
{
    public function sortItems(iterable $items) : iterable
    {
        // Code ...
         return  $items;
    }
}
```

See the implementation 
```php
use Pinoven\Dispatcher\Priority\Prioritize;
$prioritize = new Prioritize();
```

### Handle Priority

If the listener is a class then it should  implement: 
```php
use Pinoven\Dispatcher\Priority\ItemPriorityInterface;
ItemPriorityInterface::class;
```

If your listener/callable cannot directly implement this interface you can wrap by using:
```php
use Pinoven\Dispatcher\Priority\CallableInterface;
CallableInterface::class;
```

You may need to do some stuff on the wrapper, so you can use the factory to transformer your listener/callable to wrapper item.
```php
use Pinoven\Dispatcher\Priority\CallableItemPriorityInterface;
CallableItemPriorityInterface::class;
```


# Todo
- merge payload if `public $payload`  or add data to existing payload
- Automatic event hierarchy. It means by dealing with BeforeEvent, AfterEvent. Perhaps BetweenEvent.??
- Implement Logger
- Implement Container
- Implement CacheInterface
- Attach/detach Listener ?
- Implement  own Collection/Generator
- Clean Sample

# Contribution
 - Create issue: improvement + the reason why it should be implemented or issue + how to reproduce.
 - Create pull request  and explain the issue.
 
More information will come about how to contribute on all pinoven package.