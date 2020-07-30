<?php


namespace Pinoven\Dispatcher\Event;

use PHPUnit\Framework\TestCase;

/**
 * Class CustomEventTest
 * @package Pinoven\Dispatcher\Event
 */
class CustomEventTest extends TestCase
{

    public function testGetPayload()
    {
        $event = new CustomEvent('my-event', 22, array(11, 22));
        $this->assertEquals([22, [11, 22]], $event->getPayload());
    }
}
