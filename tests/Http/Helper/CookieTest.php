<?php
namespace Neat\Test\Http\Helper;

use DateTime;
use Neat\Http\Helper\Cookie;

class CookieTest extends \PHPUnit_Framework_TestCase
{
    /** @var Cookie */
    private $subject;

    /** @var DateTime */
    private $dateTime;

    protected function setUp()
    {
        date_default_timezone_set('Europe/Berlin');
        $this->subject  = new Cookie;
        $this->dateTime = new DateTime('now');

        $this->subject->name     = 'cookie_name';
        $this->subject->value    = 'cookie_value';
        $this->subject->expire   = $this->dateTime;
        $this->subject->path     = 'cookie_path';
        $this->subject->domain   = 'cookie_domain';
        $this->subject->secure   = true;
        $this->subject->httponly = true;
    }

    public function testGetCookieValues()
    {
        $this->assertSame('cookie_name', $this->subject->name);
        $this->assertSame('cookie_value', $this->subject->value);
        $this->assertSame($this->dateTime->getTimestamp(), $this->subject->expire);
        $this->assertSame('cookie_path', $this->subject->path);
        $this->assertSame('cookie_domain', $this->subject->domain);
        $this->assertSame(true, $this->subject->secure);
        $this->assertSame(true, $this->subject->httponly);
    }

    public function testToArray()
    {
        $expected = [
            'name'     => 'cookie_name',
            'value'    => 'cookie_value',
            'expire'   => $this->dateTime->getTimestamp(),
            'path'     => 'cookie_path',
            'domain'   => 'cookie_domain',
            'secure'   => true,
            'httponly' => true,
        ];

        $this->assertSame($expected, $this->subject->toArray());
    }
}
