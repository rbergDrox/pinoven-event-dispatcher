<?php


namespace Pinoven\Dispatcher\Priority;

/**
 * Class CallableItemPriorityFactory
 * @package Pinoven\Dispatcher\Priority
 */
class CallableItemPriorityFactory implements CallableItemPriorityInterface
{

    /**
     * @inheritDoc
     */
    public function wrap(callable $callable): ItemPriorityInterface
    {
        return new CallableItemPriority($callable);
    }
}
