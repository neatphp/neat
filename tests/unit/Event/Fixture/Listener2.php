<?php
namespace Neat\Test\Event\Fixture;

use Neat\Event\Event;

class Listener2
{
    public function onEvent1Action(Event $event)
    {
        $event->setParam('param2', 'listener2_event1_value2');
    }

    public function onEvent2Action(Event $event)
    {
        $event->setParam('param2', 'listener2_event2_value2');
    }
}