<?php
namespace Neat\Test\Http\Helper;

use DateTime;
use Neat\Http\Helper\Cookie;

class CookieTest extends \PHPUnit_Framework_TestCase
{
    /** @var Cookie */
    private $subject;

    protected function setUp()
    {
        date_default_timezone_set('Europe/Berlin');
        $this->subject = new Cookie;
        $this->subject->name = 'cookie_name';
        $this->subject->value = 'cookie_value';
        $this->subject->expire = new DateTime('now');
        $this->subject->path = 'cookie_path';
        $this->subject->domain = 'cookie_domain';
        $this->subject->secure = true;
        $this->subject->httponly = true;
    }

    /**
     * @test
     */
    public function getValue()
    {
        $this->assertSame('cookie_name', $this->subject->name);
        $this->assertSame('cookie_value', $this->subject->value);
        $this->assertInternalType('int', $this->subject->expire);
        $this->assertSame('cookie_path', $this->subject->path);
        $this->assertSame('cookie_domain', $this->subject->domain);
        $this->assertSame(true, $this->subject->secure);
        $this->assertSame(true, $this->subject->httponly);
    }
}
