<?php


namespace Pinoven\Dispatcher\Priority;

use Traversable;

/**
 * Interface PrioritizeInterface
 * @package Pinoven\Dispatcher\Priority
 *
 */
interface PrioritizeInterface
{

    /**
     * Sort ListenerProvider list based on priority.
     *
     * @param iterable<ItemPriorityInterface> $items
     * @return ItemPriorityInterface[]
     */
    public function sortItems(iterable $items): iterable;
}
