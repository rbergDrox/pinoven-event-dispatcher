<?php


namespace Pinoven\Dispatcher\Event;

class CustomEvent implements EventInterface, EventHasTypeInterface
{
    /**
     * @var string
     */
    private $event;

    public function __construct(string $event)
    {
        $this->event = $event;
    }

    /**
     * @inheritDoc
     */
    public function eventType(): string
    {
        return $this->event;
    }

    /**
     * @inheritDoc
     */
    public function tag(): string
    {
        return lcfirst(
            str_replace(
                ' ',
                '',
                ucwords(preg_replace('/[^a-zA-Z0-9]/', ' ', $this->event))
            )
            . 'Handler'
        );
    }
}
