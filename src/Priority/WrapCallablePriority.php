<?php


namespace Pinoven\Dispatcher\Priority;

/**
 * Class ItemCallablePriority
 * @package Pinoven\Dispatcher\Priority
 */
class WrapCallablePriority implements ItemPriorityInterface, WrapCallableInterface
{
    protected $priority = 0;

    /**
     * @var callable
     */
    private $callable;

    /**
     * WrapCallablePriority constructor.
     * @param callable $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @inheritDoc
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @inheritDoc
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * Return the callable that has been wrapped.
     * @param object $event
     */
    public function __invoke(object $event)
    {
        $callable = $this->getCallableItem();
        $callable($event);
    }

    /**
     * @inheritDoc
     */
    public function getCallableItem(): callable
    {
        return $this->callable;
    }
}
