<?php


namespace Pinoven\Dispatcher\Priority;

use Psr\EventDispatcher\ListenerProviderInterface;

interface ItemPriorityInterface
{

    /**
     * Get the priority.
     *
     * @return int
     */
    public function getPriority(): int;

    /**
     * Set the priority.
     * @param int $priority
     */
    public function setPriority(int $priority): void;
}
