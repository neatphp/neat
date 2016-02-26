<?php
namespace Neat\Test\Base;

use Neat\Test\Base\Fixture\Component\Component;
use Neat\Test\Base\Fixture\Component\Property;

class ComponentTest extends AbstractComponentTest
{
    /** @var Component */
    protected $subject;

    protected function setUp()
    {
        $this->subject = new Component;

        parent::setUp();
    }

    public function testSet_withExistingPropertyAndValidValue()
    {
        $this->subject->setProperty('property1', new Property);
        $this->assertInstanceOf('Neat\Test\Base\Fixture\Component\Property', $this->subject->property1);
        $this->subject->property2 = 'string';
        $this->assertSame('string', $this->subject->property2);
        $this->subject->property3 = [];
        $this->assertSame([], $this->subject->property3);
    }

    public function testSet_withExistingPropertyAndInvalidValue_throwsException()
    {
        $this->setExpectedException('Neat\Data\Exception\InvalidArgumentException');
        $this->subject->setProperty('property1', 'test');
    }

    public function testSet_withExistingPropertyAndValidValue_throwsException()
    {
        $this->setExpectedException('Neat\Data\Exception\ReadonlyException');
        $this->subject->setProperty('property1', new Property);
        $this->subject->setProperty('property1', new Property);
    }

    public function testSet_withNonExistingOffset_throwsException()
    {
        $this->setExpectedException('Neat\Data\Exception\OutOfBoundsException');
        $this->subject->property = 'test';
    }

    public function testGet_withExistingProperty_throwsException()
    {
        $this->setExpectedException('Neat\Data\Exception\InvalidArgumentException');
        $this->subject->property1;
    }

    public function testGet_withNonExistingProperty_throwsException()
    {
        $this->setExpectedException('Neat\Data\Exception\OutOfBoundsException');
        $this->subject->property;
    }

    public function testGetConfig_returnsConfigOrOptionValue()
    {
        $this->mockedConfig
            ->shouldReceive('get')
            ->with('option')
            ->andReturn('value');

        $this->assertInstanceOf('Neat\Config\Config', $this->subject->getConfig());
        $this->assertSame('value', $this->subject->getConfig('option'));
    }

    public function testDispatchEvent_returnsEvent()
    {
        $this->mockedEventDispatcher
            ->shouldReceive('dispatchEvent')
            ->with('event', $this->subject, [])
            ->andReturn($this->mockedEvent);

        $this->assertInstanceOf('Neat\Event\Event', $this->subject->dispatchEvent('event'));
    }
}
