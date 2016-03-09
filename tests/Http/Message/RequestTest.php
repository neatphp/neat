<?php
namespace Neat\Test\Http;

use Neat\Http\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /** @var Request */
    private $subject;

    protected function setUp()
    {
        $this->subject = new Request;
    }

    /**
     * @test
     */
    public function accessPathParams()
    {
        $this->assertInstanceOf('Neat\Data\Data', $this->subject->pathParams);
    }

    /**
     * @test
     */
    public function accessGetParams()
    {
        $this->assertInstanceOf('Neat\Data\Data', $this->subject->getParams);
    }

    /**
     * @test
     */
    public function accessPostParams()
    {
        $this->assertInstanceOf('Neat\Data\Data', $this->subject->postParams);
    }

    /**
     * @test
     */
    public function accessCookieParams()
    {
        $this->assertInstanceOf('Neat\Data\Data', $this->subject->cookieParams);
    }

    /**
     * @test
     */
    public function accessFilesParams()
    {
        $this->assertInstanceOf('Neat\Data\Data', $this->subject->filesParams);
    }

    /**
     * @test
     */
    public function accessServerParams()
    {
        $this->assertInstanceOf('Neat\Data\Data', $this->subject->serverParams);
    }
}