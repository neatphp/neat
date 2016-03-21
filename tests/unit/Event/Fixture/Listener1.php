<?php
namespace Neat\Test\Event\Fixture;

use Neat\Event\Event;

class Listener1
{
    public function onEvent1Action(Event $event)
    {
        $event->setParam('param1', 'listener1_event1_value1');
        $event->setCancel(true);
    }

    public function onEvent2Action(Event $event)
    {
        $event->setParam('param1', 'listener1_event2_value1');
        $event->setCancel(false);
    }
}