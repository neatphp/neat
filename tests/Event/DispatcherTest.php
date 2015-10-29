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

    /**
     * @test
     */
    public function dispatchEvent1()
    {
        $values = [
            'value1'  => 'event1_value1',
            'value2'  => 'event2_value2',
            'value3'  => 'event3_value3',
        ];

        $event = $this->subject->dispatchEvent('event1', $values);
        $this->assertSame('event1', $event->getName());
        $this->assertNull($event->getSubject());
        $this->assertSame('listener1_event1_value1', $event->value1);
        $this->assertSame('listener1_event1_value2', $event->value2);
        $this->assertSame('listener1_event1_value3', $event->value3);
        $this->assertTrue($event->isCancelled());
    }

    /**
     * @test
     */
    public function dispatchEvent2()
    {
        $values = [
            'value1'  => 'event2_value1',
            'value2'  => 'event2_value2',
            'value3'  => 'event2_value3',
        ];

        $event = $this->subject->dispatchEvent('event2', $values);
        $this->assertSame('event2', $event->getName());
        $this->assertNull($event->getSubject());
        $this->assertSame('listener2_event2_value1', $event->value1);
        $this->assertSame('listener2_event2_value2', $event->value2);
        $this->assertSame('listener2_event2_value3', $event->value3);
        $this->assertFalse($event->isCancelled());
    }
}