<?php


namespace Pinoven\Dispatcher\Priority;

use Traversable;

/**
 * Class PrioritizeProvider
 * @package Pinoven\Dispatcher\Priority
 */
class PrioritizeProvider implements PrioritizeInterface
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
                if ($itemPriority1 instanceof ItemPriorityInterface
                    && $itemPriority2 instanceof ItemPriorityInterface) {
                    return $itemPriority1->getPriority() < $itemPriority2->getPriority() ? 1: -1;
                }
                if ($itemPriority1 instanceof ItemPriorityInterface) {
                    return $itemPriority1->getPriority() < 0 ? 1: -1;
                }
                if ($itemPriority2 instanceof ItemPriorityInterface) {
                    return 0 < $itemPriority2->getPriority() ? 1: -1;
                }
                return 0;
            }
        );

        return $items;
    }
}
