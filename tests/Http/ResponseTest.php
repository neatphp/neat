<?php
namespace Neat\Test\Http;

use Neat\Http\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /** @var Response */
    private $subject;

    protected function setUp()
    {
        $this->subject = new Response;
    }

    /**
     * @test
     */
    public function setStatusCode_validCode()
    {
        $this->subject->statusCode = 400;
        $this->assertSame(400, $this->subject->statusCode);
    }

    /**
     * @test
     * @expectedException \Neat\Data\Exception\InvalidArgumentException
     */
    public function setStatusCode_invalidCode()
    {
        $this->subject->statusCode = 0;
    }
}
