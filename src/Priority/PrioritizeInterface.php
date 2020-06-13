<?php


namespace Pinoven\Dispatcher\Priority;

use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * Interface PrioritizeInterface
 * @package Pinoven\Dispatcher\Priority
 *
 */
interface PrioritizeInterface
{

    /**
     * Sort ListenerProvider list based on there
     *
     * @param ListenerProviderInterface[] $providers
     * @return ListenerProviderInterface[]
     */
    public function sortItems(array $providers): array;
}
