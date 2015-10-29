<?php
namespace Neat\Test\Event\Fixture;

use Neat\Event\Event;

class Listener1
{
    public function onEvent1Action(Event $event)
    {
        $event->value1 = 'listener1_event1_value1';
        $event->value2 = 'listener1_event1_value2';
        $event->value3 = 'listener1_event1_value3';
        $event->setCancel(true);
    }

    public function onEvent2Action(Event $event)
    {
        $event->value1 = 'listener1_event2_value1';
        $event->value2 = 'listener1_event2_value2';
        $event->value3 = 'listener1_event2_value3';
        $event->setCancel(false);
    }
}