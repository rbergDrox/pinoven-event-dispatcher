<?php


namespace Pinoven\Dispatcher\Event;

class CustomEvent implements EventInterface, EventHasTypeInterface, EventHasPayloadInterface
{
    /**
     * @var string
     */
    private $event;

    /**
     * @var array
     */
    private $payload;

    public function __construct(string $event, ...$payload)
    {
        $this->event = $event;
        $this->payload = $payload;
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

    /**
     * @inheritDoc
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
}
