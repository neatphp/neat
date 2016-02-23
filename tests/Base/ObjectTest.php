<?php
namespace Neat\Test\Object;

use Neat\Test\Base\Fixture\Object\TestObject;

class ObjectTest extends \PHPUnit_Framework_TestCase
{
    /** @var TestObject */
    private $subject;

    protected function setUp()
    {
        $this->subject = new TestObject;
        $this->subject->property1 = 'property1_value';
        $this->subject->property2 = 'property2_value';
    }

    public function testClone_returnsObject()
    {
        $clone = clone $this->subject;
        $this->assertSame('property1_value', $clone->property1);
        $this->assertSame('property2_value', $clone->property2);
    }
}