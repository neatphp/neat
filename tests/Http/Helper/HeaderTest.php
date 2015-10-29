<?php
namespace Neat\Test\Http\Helper;

use Neat\Http\Helper\Header;

class HeaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var Header */
    private $subject;

    protected function setUp()
    {
        $this->subject = new Header;

        $this->subject->accept = 'accept_value';
        $this->subject->content_type = ['content_type_value1', 'content_type_value2'];
    }

    /**
     * @test
     */
    public function getValue()
    {
        $this->assertSame('accept_value', $this->subject->accept);
        $this->assertSame(['content_type_value1', 'content_type_value2'], $this->subject->content_type);
    }

    /**
     * @test
     */
    public function toArray()
    {
        $values = $this->subject->toArray();
        $this->assertSame('Accept: accept_value', $values['accept']);
        $this->assertSame('Content-Type: content_type_value1; charset=content_type_value2', $values['content_type']);
    }
}
