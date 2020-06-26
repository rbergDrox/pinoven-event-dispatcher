<?php


namespace Pinoven\Dispatcher\Priority;

/**
 * Interface WrapCallableFactoryInterface
 * @package Pinoven\Dispatcher\Priority
 */
interface WrapCallableFactoryInterface
{

    /**
     * Create a wrap callable item.
     *
     * @param callable $callable
     * @return ItemPriorityInterface
     */
    public function createWrapCallablePriority(callable $callable): ItemPriorityInterface;
}
