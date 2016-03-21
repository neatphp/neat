<?php

namespace Message;


/**
 * Class StringStreamTest
 *
 * @group Message
 */
class StringStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test creating Stream
     */
    public function testStream()
    {
        $stream = new StringStream('test');
        $this->assertEquals('test', $stream->getContents());
        $this->assertEquals('test', (string) $stream);
    }

    /**
     * Test read
     */
    public function testRead()
    {
        $stream = new StringStream('test');
        $this->assertEquals('te', $stream->read(2));
    }
}
