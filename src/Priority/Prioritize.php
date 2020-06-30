<?php


namespace Pinoven\Dispatcher\Priority;

use Traversable;

/**
 * Class Prioritize
 * @package Pinoven\Dispatcher\Priority
 */
class Prioritize implements PrioritizeInterface
{

    /**
     * Any traversable to array.
     *
     * @param iterable $itemsGen
     * @return array
     */
    private function traversableToArray(iterable $itemsGen)
    {
        $items = [];
        foreach ($itemsGen as $item) {
            $items[] = $item;
        }
        return $items;
    }

    /**
     * Determine the position between 2 items.
     * @param $itemPriority1
     * @param $itemPriority2
     * @return int
     */
    private function determineRankPriority($itemPriority1, $itemPriority2)
    {
        $rank = 0;
        if ($itemPriority1 instanceof ItemPriorityInterface
            && $itemPriority2 instanceof ItemPriorityInterface) {
            $rank = $itemPriority1->getPriority() < $itemPriority2->getPriority() ? 1: -1;
        } elseif ($itemPriority1 instanceof ItemPriorityInterface) {
            $rank = $itemPriority1->getPriority() < 0 ? 1: -1;
        } elseif ($itemPriority2 instanceof ItemPriorityInterface) {
            $rank = 0 < $itemPriority2->getPriority() ? 1: -1;
        }
        return $rank;
    }

    /**
     * @inheritDoc
     */
    public function sortItems(iterable $items): iterable
    {
        if (!is_array($items) && $items instanceof Traversable) {
            $items = $this->traversableToArray($items);
        }
        usort(
            $items,
            function ($itemPriority1, $itemPriority2) {
                return $this->determineRankPriority($itemPriority1, $itemPriority2);
            }
        );

        return $items;
    }
}
