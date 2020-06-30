<?php


namespace Pinoven\Dispatcher\Listener;

use Closure;
use Fig\EventDispatcher\ParameterDeriverTrait;
use Pinoven\Dispatcher\Priority\ItemPriorityInterface;
use Pinoven\Dispatcher\Priority\CallableItemPriorityInterface;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;

/**
 * Class ProxyListeners
 * @package Pinoven\Dispatcher\Listener
 */
class ProxyListeners implements ProxyListener, ProxyListenerHasContainer
{
    use ParameterDeriverTrait;

    /**
     * @var ContainerInterface|null
     */
    protected $container;

    /**
     * @var CallableItemPriorityInterface|null
     */
    private $wrapCallableFactory;

    /**
     * ProxyListenersMapper constructor.
     * @param ContainerInterface|null $container
     * @param CallableItemPriorityInterface|null $wrapCallableFactory
     */
    public function __construct(
        ?ContainerInterface $container = null,
        ?CallableItemPriorityInterface $wrapCallableFactory = null
    ) {
        $this->container = $container;
        $this->wrapCallableFactory = $wrapCallableFactory;
    }

    /**
     * @inheritDoc
     */
    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     * @throws ReflectionException
     */
    public function getCallableListeners(string $eventType, iterable $listeners, string $tag): iterable
    {
        foreach ($listeners as $listener) {
            $callable = $this->listenerToCallable($eventType, $listener, $tag);

            if (!$callable) {
                continue;
            }
            $type = $this->getParameterType($callable);
            if ($type == $eventType) {
                if ($this->wrapCallableFactory && !($callable instanceof ItemPriorityInterface)) {
                    $callable = $this->wrapCallableFactory->wrap($callable);
                }
                yield $callable;
            }
        }
    }

    /**
     * @inheritDoc
     * @throws ReflectionException
     */
    public function listenerToCallable(string $eventType, $listener, string $tag): ?callable
    {
        if ($tag == '__invoke' && (is_object($listener) || $listener instanceof Closure) && is_callable($listener)) {
            return $listener;
        }
        $itemCallable = $this->retrieveFromContainer($listener, $tag);
        return $itemCallable? : $this->retrieveFromClass($listener, $tag);
    }

    /**
     * Retrieve/Construct callable from class to use with static method.
     *
     * @param string $listener
     * @param string $tag
     * @return callable|null
     * @throws ReflectionException
     */
    protected function retrieveClassWithStaticMethod(string $listener, string $tag): ?callable
    {
        $reflection = class_exists($listener) ? new ReflectionClass($listener) : null;
        if ($reflection && $reflection->hasMethod($tag) && $reflection->getMethod($tag)->isStatic()) {
            return [$listener, $tag];
        }
            //todo: constructor has no parameters can be instantiated.
            //todo: manage __invoke. dependency with above issue.
        return null;
    }

    /**
     * Retrieve/Construct callable from class to use with  public method.
     *
     * @param object $listener
     * @param string $tag
     * @return callable|null
     * @throws ReflectionException
     */
    protected function retrieveFromClassWithPublicMethod(object $listener, string $tag): ?callable
    {
        $callable = null;
        $reflection =  new ReflectionObject($listener);
        if ($reflection->hasMethod($tag) && $reflection->getMethod($tag)->isPublic()) {
            $callable = [$listener, $tag];
        }
        return $callable;
    }

    /**
     * Retrieve/Construct callable from class to use with static method.
     *
     * @param $listener
     * @param string $tag
     * @return callable|null
     * @throws ReflectionException
     */
    protected function retrieveFromClass($listener, string $tag): ?callable
    {
        if (is_object($listener) && $callableObject = $this->retrieveFromClassWithPublicMethod($listener, $tag)) {
            return $callableObject;
        }
        if (is_string($listener)  && $CallableClass = $this->retrieveClassWithStaticMethod($listener, $tag)) {
            return $CallableClass;
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function retrieveFromContainer($listener, string $tag): ?callable
    {
        $item = null;
        if ($this->container && is_string($listener) && $this->container->has($listener)) {
            $item = $this->container->get($listener);
        }
        if (!is_callable($item) && is_object($item)) {
            return $this->createCallableFromObject($item, $tag);
        } elseif (is_callable($item)) {
            return $item;
        }
        //todo: constructor has parameters can they be instantiated?
        return null;
    }

    /**
     * Retrieve/Construct callable from an object if the "tag" method exists.
     * @param object $item
     * @param string $tag
     * @return array
     * @throws ReflectionException
     * @see ProxyListeners::retrieveFromContainer();
     *
     */
    protected function createCallableFromObject(object $item, string $tag): ?callable
    {
        $reflectionItem = new ReflectionObject($item);
        if ($reflectionItem->hasMethod($tag) && $reflectionItem->getMethod($tag)->isPublic()) {
            return [$item, $tag];
        }
        return null;
    }
}
