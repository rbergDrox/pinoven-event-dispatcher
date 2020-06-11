<?php


namespace Pinoven\Dispatcher\Listener;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;

/**
 * Interface ProxyListenerWithContainer
 * @package Pinoven\Dispatcher\Listener
 */
interface ProxyListenerWithContainer extends ProxyListener
{
    /**
     * Declare PSR-11 container. It should be use to retrieve callable or any instance to use as a listener.
     *
     * @see https://github.com/container-interop/fig-standards/blob/master/proposed/container.md
     *
     * @param ContainerInterface $container
     * @return $this
     */
    public function setContainer(ContainerInterface $container): ProxyListener;

    /**
     * Retrieve/Construct callable from PSR-11 container.
     *
     * @param mixed $listener
     * @param string $tag
     * @return callable|null
     * @throws ReflectionException
     * @throws NotFoundExceptionInterface  No entry was found for the  provided listener identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     */
    public function retrieveFromContainer($listener, string $tag): ?callable;
}
