<?php
namespace Neat\Test\Http\Message;

use Neat\Http\Message\Uri;

class UriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Uri
     */
    private $subject;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->subject = new Uri(
            'http://user:password@wwww.test.com:80/path1/path2/path3?query1=value1&query2=value2&query3=value3#fragment');
    }

    /**
     * Test __construct
     */
    public function testConstructorWithMalformedUri()
    {

        $this->subject = new Uri('http:///test.com"');
    }

    /**
     * Test __toString
     */
    public function testStringCasting()
    {
        $this->assertSame(

            (string)$this->subject
        );
    }

    /**
     * Test getScheme
     */
    public function testGetScheme()
    {
        $this->assertSame('http', $this->subject->getScheme());
    }

    /**
     * Test getAuthority
     */
    public function testGetAuthority()
    {
        $this->assertSame('user:password@wwww.test.com', $this->subject->getAuthority());
        $this->assertSame('user:password@wwww.test.com:8080', $this->subject->withPort(8080)->getAuthority());
        $this->assertSame('', $this->subject->withHost('')->getAuthority());
    }

    /**
     * Test getUserInfo
     */
    public function testGetUserInfo()
    {
        $this->assertSame('user:password', $this->subject->getUserInfo());
    }

    /**
     * Test getHost
     */
    public function testGetHost()
    {
        $this->assertSame('wwww.test.com', $this->subject->getHost());
    }

    /**
     * Test getPort
     */
    public function testGetPort()
    {
        $this->assertNull($this->subject->getPort());
    }

    /**
     * Test getPath
     */
    public function testGetPath()
    {
        $this->assertSame('/path1/path2/path3', $this->subject->getPath());
    }

    /**
     * Test getQuery
     */
    public function testGetQuery()
    {
        $this->assertSame('query1=value1&query2=value2&query3=value3', $this->subject->getQuery());
    }

    /**
     * Test getFragment
     */
    public function testGetFragment()
    {
        $this->assertSame('fragment', $this->subject->getFragment());
    }

    /**
     * Test withScheme
     */
    public function testWithScheme()
    {
        $this->assertSame($this->subject, $this->subject->withScheme('http'));
        $this->assertNotSame($this->subject, $this->subject->withScheme('https'));
        $this->assertSame('https', $this->subject->withScheme('https')->getScheme());
    }

    /**
     * Test withScheme
     */
    public function testWithSchemeWithInvalidValue()
    {
        $this->subject->withScheme(array());
    }

    /**
     * Test withScheme
     */
    public function testWithUserInfo()
    {
        $this->assertSame($this->subject, $this->subject->withUserInfo('user', 'password'));
        $this->assertNotSame($this->subject, $this->subject->withUserInfo('new_user', 'new_password'));
        $this->assertSame('new_user:new_password', $this->subject->withUserInfo('new_user', 'new_password')->getUserInfo());
    }

    /**
     * Test withHost
     */
    public function testWithHost()
    {
        $this->assertSame($this->subject, $this->subject->withHost('wwww.test.com'));
        $this->assertNotSame($this->subject, $this->subject->withHost('wwww.test2.com'));
        $this->assertSame('wwww.test2.com', $this->subject->withHost('wwww.test2.com')->getHost());
    }

    /**
     * Test withPort
     */
    public function testWithPort()
    {
        $this->assertSame($this->subject, $this->subject->withPort(80));
        $this->assertNotSame($this->subject, $this->subject->withPort(8080));
        $this->assertSame(8080, $this->subject->withPort(8080)->getPort());
    }

    /**
     * Test withPath
     */
    public function testWithPath()
    {
        $this->assertSame($this->subject, $this->subject->withPath('/path1/path2/path3'));
        $this->assertNotSame($this->subject, $this->subject->withPath('/path'));
        $this->assertSame('/path', $this->subject->withPath('/path')->getPath());
    }

    /**
     * Test withQuery
     */
    public function testWithQuery()
    {
        $this->assertSame($this->subject, $this->subject->withQuery('query1=value1&query2=value2&query3=value3'));
        $this->assertNotSame($this->subject, $this->subject->withQuery(''));
        $this->assertSame('', $this->subject->withQuery('')->getQuery());
    }

    /**
     * Test withFragment
     */
    public function testWithFragment()
    {
        $this->assertSame($this->subject, $this->subject->withFragment('fragment'));
        $this->assertNotSame($this->subject, $this->subject->withFragment('new_fragment'));
        $this->assertSame('new_fragment', $this->subject->withFragment('new_fragment')->getFragment());
    }
}
