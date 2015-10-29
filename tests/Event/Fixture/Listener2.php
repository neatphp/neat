<?php
namespace Neat\Test\Event\Fixture;

use Neat\Event\Event;

class Listener2
{
    public function onEvent1Action(Event $event)
    {
        $event->value1 = 'listener2_event1_value1';
        $event->value2 = 'listener2_event1_value2';
        $event->value3 = 'listener2_event1_value3';
    }

    public function onEvent2Action(Event $event)
    {
        $event->value1 = 'listener2_event2_value1';
        $event->value2 = 'listener2_event2_value2';
        $event->value3 = 'listener2_event2_value3';
    }
}