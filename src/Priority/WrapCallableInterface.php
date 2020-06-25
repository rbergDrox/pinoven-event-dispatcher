<?php


namespace Pinoven\Dispatcher\Priority;

/**
 * Interface WrapCallableInterface
 * @package Pinoven\Dispatcher\Priority
 */
interface WrapCallableInterface
{

    /**
     * Get the original callable with right parameters.
     *
     * @return callable
     */
    public function getCallableItem(): callable;
}
