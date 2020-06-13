<?php


namespace Pinoven\Dispatcher\Priority;

/**
 * Class PrioritizeProvider
 * @package Pinoven\Dispatcher\Priority
 */
class PrioritizeProvider implements PrioritizeInterface
{

    /**
     * @inheritDoc
     */
    public function sortItems(array $providers): array
    {
        usort($providers, function (ItemPriorityInterface $itemPriority1, ItemPriorityInterface $itemPriority2) {
            return $itemPriority1->getPriority() < $itemPriority2->getPriority() ? 1: -1;
        });
        return $providers;
    }
}
