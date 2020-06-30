<?php


namespace Pinoven\Dispatcher\Priority;

/**
 * Interface CallableInterface
 * @package Pinoven\Dispatcher\Priority
 */
interface CallableInterface
{

    /**
     * Get the original callable with right parameters.
     *
     * @return callable
     */
    public function getCallableItem(): callable;
}
