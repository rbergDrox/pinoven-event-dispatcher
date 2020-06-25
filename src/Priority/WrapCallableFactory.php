<?php


namespace Pinoven\Dispatcher\Priority;

/**
 * Class WrapCallableFactory
 * @package Pinoven\Dispatcher\Priority
 */
class WrapCallableFactory implements WrapCallableFactoryInterface
{

    /**
     * @inheritDoc
     */
    public function createWrapCallablePriority(callable $callable): ItemPriorityInterface
    {
        return new WrapCallablePriority($callable);
    }
}
