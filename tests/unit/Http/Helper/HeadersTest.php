<?php
namespace Neat\Test\Http\Helper;

use Neat\Http\Helper\Headers;

class HeadersTest extends \PHPUnit_Framework_TestCase
{
    /** @var Headers */
    private $subject;

    protected function setUp()
    {
        $this->subject = new Headers;
        $this->subject->accept = 'accept_value';
        $this->subject->content_type = ['content_type_value1', 'content_type_value2'];
    }

    public function testGetHeaderValue()
    {
        $this->assertSame('accept_value', $this->subject->accept);
        $this->assertSame(['content_type_value1', 'content_type_value2'], $this->subject->content_type);
    }

    public function testToArray()
    {
        $expected = [
            'accept'       => 'Accept: accept_value',
            'content_type' => 'Content-Type: content_type_value1; charset=content_type_value2',
        ];

        $this->assertSame($expected, $this->subject->toArray());
    }
}
