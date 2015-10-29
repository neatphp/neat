<?php
namespace Neat\Test\Data\Structure;

use Neat\Data\Structure\PriorityList;

class PriorityArrayTest extends \PHPUnit_Framework_TestCase
{
    /** @var PriorityList  */
    private $subject;

    protected function setUp()
    {
        $this->subject = new PriorityList;
        $this->subject->insert('value1', 1);
        $this->subject->insert('value2', 2);
        $this->subject->insert('value3', 3);
    }

    /**
     * @test
     */
    public function insert()
    {
        $this->subject->insert('value4', 0);
        $this->subject->insert('value5', 1);
        $this->subject->insert('value6', 4);
        $expected = ['value6', 'value3', 'value2', 'value1', 'value5', 'value4'];
        $this->assertSame($expected, $this->subject->toArray());
    }

    /**
     * @test
     */
    public function remove()
    {
        $this->subject->insert('value4', 1);
        $this->subject->insert('value4', 2);
        $this->subject->insert('value4', 3);
        $this->subject->remove('value4');
        $expected = ['value3', 'value2', 'value1'];
        $this->assertSame($expected, $this->subject->toArray());
    }

    /**
     * @test
     */
    public function random()
    {
        $this->assertTrue(in_array($this->subject->random(), ['value1', 'value2', 'value3']));
    }
}
