<?php


namespace Pinoven\Dispatcher\Samples;

class DefaultEvent
{
    /**
     * @var int
     */
    private $increment = 0;

    public function increment()
    {
        $this->increment++;
    }

    public function getIncrement(): int
    {
        return $this->increment;
    }
}
