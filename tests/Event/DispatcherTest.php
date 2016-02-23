<?php
namespace Neat\Test\Event;

use Neat\Event\Dispatcher;
use Neat\Test\Event\Fixture\Listener1;
use Neat\Test\Event\Fixture\Listener2;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @var Dispatcher */
    private $subject;

    protected function setUp()
    {
        $this->subject = new Dispatcher;
        $this->subject
            ->addListener('event1', [new Listener1, 'onEvent1Action'])
            ->addListener('event1', [new Listener2, 'onEvent1Action'], 10)
            ->addListener('event2', [new Listener1, 'onEvent2Action'], 10)
            ->addListener('event2', [new Listener2, 'onEvent2Action']);
    }

    public function testDispatchEvent1()
    {
        $args = [
            'param1'  => 'event1_value1',
            'param2'  => 'event1_value2',
            'param3'  => 'event1_value3',
        ];

        $event = $this->subject->dispatchEvent('event1', null, $args);
        $this->assertSame('event1', $event->getName());
        $this->assertNull($event->getSubject());
        $this->assertSame('listener1_event1_value1', $event->getParam('param1'));
        $this->assertSame('listener2_event1_value2', $event->getParam('param2'));
        $this->assertSame('event1_value3', $event->getParam('param3'));
        $this->assertTrue($event->isCancelled());
    }

    public function testDispatchEvent2()
    {
        $args = [
            'param1'  => 'event2_value1',
            'param2'  => 'event2_value2',
            'param3'  => 'event2_value3',
        ];

        $event = $this->subject->dispatchEvent('event2', null, $args);
        $this->assertSame('event2', $event->getName());
        $this->assertNull($event->getSubject());
        $this->assertSame('listener1_event2_value1', $event->getParam('param1'));
        $this->assertSame('listener2_event2_value2', $event->getParam('param2'));
        $this->assertSame('event2_value3', $event->getParam('param3'));
        $this->assertFalse($event->isCancelled());
    }
}