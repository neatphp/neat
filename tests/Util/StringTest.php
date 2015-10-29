<?php
namespace Neat\Test\Util;

use Neat\Util\Strings;

class StringTest extends \PHPUnit_Framework_TestCase
{
    /** @var Strings */
    private $subject;

    protected function setUp()
    {
        $this->subject = new Strings;
    }

    /**
     * @test
     */
    public function password_returnsString()
    {
        $this->assertInternalType('string', $this->subject->password());
        $this->assertNotEmpty($this->subject->password(), $this->subject->password());
    }

    /**
     * @test
     */
    public function censor_returnsString()
    {
        $this->assertSame('*** and ***', $this->subject->censor('firstname and Lastname', ['Firstname', 'lastname']));
    }

    /**
     * @test
     */
    public function highlight_returnsString()
    {
        $expected = '<strong>firstname</strong> and <strong>Lastname</strong>';
        $this->assertSame($expected, $this->subject->highlight('firstname and Lastname', ['Firstname', 'lastname']));
    }

    /**
     * @test
     */
    public function wordwrap_returnString()
    {
        $this->assertSame("te\nst", $this->subject->wordwrap('test', 2));
    }

    /**
     * @test
     */
    public function abbreviate_returnString()
    {
        $this->assertSame("test test...", $this->subject->abbreviate('test test test', 10));
    }

    /**
     * @test
     */
    public function toASCII_returnString()
    {
        $expected = '&#116;&#101;&#115;&#116;';
        $this->assertSame($expected, $this->subject->toASCII('test'));
    }

    /**
     * @test
     */
    public function emailToASCII_returnString()
    {
        $expected = '&#116;&#101;&#115;&#116;&#64;&#116;&#101;&#115;&#116;&#46;&#99;&#111;&#109;';
        $this->assertSame($expected, $this->subject->emailToASCII('test@test.com'));
    }

    /**
     * @test
     */
    public function calculate_returnString()
    {
        $this->assertSame(2, $this->subject->calculate('1 + 1'));
    }
}
