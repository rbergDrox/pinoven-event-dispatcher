<?php


namespace Pinoven\Dispatcher\Listener;

use Closure;
use Fig\EventDispatcher\ParameterDeriverTrait;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;

/**
 * Class ProxyCallableListenersMapper
 * @package Pinoven\Dispatcher\Listener
 */
class ProxyCallableListenersMapper implements ProxyListenerWithContainer
{
    use ParameterDeriverTrait;

    /**
     * @var ContainerInterface|null
     */
    protected $container;

    /**
     * ProxyCallableListenersMapper constructor.
     * @param ContainerInterface|null $container
     */
    public function __construct(?ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function setContainer(ContainerInterface $container): ProxyListener
    {
        $this->container = $container;
        return $this;
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
        if ($itemCallable) {
            return $itemCallable;
        }
        $classCallable = $this->retrieveFromClass($listener, $tag);
        if ($classCallable) {
            return $classCallable;
        }
        return null;
    }

    /**
     * Retrieve/Construct callable from class to use with static or public method.
     *
     * @param $listener
     * @param string $tag
     * @return callable|null
     * @throws ReflectionException
     */
    protected function retrieveFromClass($listener, string $tag): ?callable
    {
        if (is_object($listener)) {
            $reflectionClass = new ReflectionObject($listener);
            if ($reflectionClass->hasMethod($tag) && $reflectionClass->getMethod($tag)->isPublic()) {
                return [$listener, $tag];
            }
        }
        if (is_string($listener) && class_exists($listener)) {
            $reflectionClass = new ReflectionClass($listener);
            if ($reflectionClass->hasMethod($tag) && $reflectionClass->getMethod($tag)->isStatic()) {
                return [$listener, $tag];
            }
            //todo: constructor has no parameters can be instantiated.
            //todo: manage __invoke. dependency with above issue.
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function retrieveFromContainer($listener, string $tag): ?callable
    {
        if (!$this->container || !is_string($listener) || !$this->container->has($listener)) {
            return null;
        }
        $item = $this->container->get($listener);

        if (is_callable($item) && (!is_array($item) || (is_array($item) && $item[1] == $tag))) {
            return $item;
        }
        if (is_object($item)) {
            return $this->createCallableFromObject($item, $tag);
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
     * @see ProxyCallableListenersMapper::retrieveFromContainer();
     *
     */
    protected function createCallableFromObject(object $item, string $tag): ?callable
    {
        if ($reflectionItem = new ReflectionObject($item)) {
            $method = $reflectionItem->hasMethod($tag) ? $reflectionItem->getMethod($tag) : null;
            if ($method && $method->isPublic()) {
                return [$item, $tag];
            }
        }
        return null;
    }
}
