<?php


namespace Pinoven\Dispatcher\Listener;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;

/**
 * Interface ProxyListenerHasContainer
 * @package Pinoven\Dispatcher\Listener
 */
interface ProxyListenerHasContainer
{
    /**
     * Declare PSR-11 container. It should be use to retrieve callable or any instance to use as a listener.
     *
     * @see https://github.com/container-interop/fig-standards/blob/master/proposed/container.md
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function setContainer(ContainerInterface $container): void;

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
