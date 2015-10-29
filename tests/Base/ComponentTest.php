<?php
namespace Neat\Test\Base;

use Neat\Test\Base\Fixture\Component\Component;
use Neat\Test\Base\Fixture\Component\Property1;
use Neat\Test\Base\Fixture\Component\Property2;
use Neat\Test\Base\Fixture\Component\Property3;

class ComponentTest extends AbstractComponentTest
{
    /** @var Component */
    protected $subject;

    protected function setUp()
    {
        $this->subject = new Component;

        parent::setUp();
    }

    /**
     * @test
     */
    public function set_existingPropertyAndValidValue()
    {
        $this->subject->property1 = new Property1;
        $this->assertInstanceOf('Neat\Test\Base\Fixture\Component\Property1', $this->subject->property1);
        $this->subject->property2 = new Property2;
        $this->assertInstanceOf('Neat\Test\Base\Fixture\Component\Property2', $this->subject->property2);
        $this->subject->property3 = new Property3;
        $this->assertInstanceOf('Neat\Test\Base\Fixture\Component\Property3', $this->subject->property3);
    }

    /**
     * @test
     * @expectedException \Neat\Data\Exception\InvalidArgumentException
     */
    public function set_existingPropertyAndInvalidValue_throwsException()
    {
        $this->subject->property1 = 'test';
    }

    /**
     * @test
     * @expectedException \Neat\Data\Exception\ReadonlyException
     */
    public function set_existingPropertyWithValue_throwsException()
    {
        $this->subject->property1 = new Property1;
        $this->subject->property1 = new Property1;
    }

    /**
     * @test
     * @expectedException \Neat\Data\Exception\OverflowException
     */
    public function set_nonExistingOffset_throwsException()
    {
        $this->subject->property = 'test';
    }

    /**
     * @test
     */
    public function get_existingProperty()
    {
        $this->subject->property1;
        $this->subject->property2;
        $this->subject->property3;
    }

    /**
     * @test
     * @expectedException \Neat\Data\Exception\OutOfBoundsException
     */
    public function get_nonExistingOffset_throwsException()
    {
        $this->subject->property;
    }

    /**
     * @test
     */
    public function getConfig_returnConfigOrOptionValue()
    {
        $this->mockedConfig
            ->shouldReceive('get')
            ->with('option')
            ->andReturn('value');

        $this->assertInstanceOf('Neat\Config\Config', $this->subject->getConfig());
        $this->assertSame('value', $this->subject->getConfig('option'));
    }

    /**
     * @test
     */
    public function dispatchEvent_returnEvent()
    {
        $this->mockedEventDispatcher
            ->shouldReceive('dispatchEvent')
            ->with('event', [], $this->subject)
            ->andReturn($this->mockedEvent);

        $this->assertInstanceOf('Neat\Event\Event', $this->subject->dispatchEvent('event'));
    }
}
