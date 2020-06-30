<?php


namespace Pinoven\Dispatcher\Priority;

/**
 * Interface CallableItemPriorityInterface
 * @package Pinoven\Dispatcher\Priority
 */
interface CallableItemPriorityInterface
{

    /**
     * Wrap callable item to an item with priority.
     *
     * @param callable $callable
     * @return ItemPriorityInterface
     */
    public function wrap(callable $callable): ItemPriorityInterface;
}
