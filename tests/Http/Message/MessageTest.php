<?php
/*
 * Copyright (c) 2015 babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of babymarkt.de GmbH
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace Babymarkt\Insect\Cable\Tests\Unit\Communication\Message;

use Babymarkt\Insect\Cable\Communication\Message\Message;
use Psr\Http\Message\StreamInterface;

/**
 * Class MessageTest
 *
 * @package Babymarkt\Insect\Cable\Tests\Unit
 * @group Message
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Message
     */
    private $messageMock;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->messageMock = $this->getMockForAbstractClass('Babymarkt\Insect\Cable\Communication\Message\Message');
    }

    /**
     * Test withBody
     */
    public function testWithBody()
    {
        /** @var StreamInterface $stream */
        $stream = $this->getMock('Psr\Http\Message\StreamInterface');

        $message = $this->messageMock->withBody($stream);

        $this->assertNotSame($message, $this->messageMock);
        $this->assertNotEquals($stream, $this->messageMock->getBody());
        $this->assertEquals($stream, $message->getBody());
    }

    /**
     *
     * @return void
     */
    public function testHeaders()
    {
        $message1 = $this->messageMock->withHeader('test', 'test');

        // Check if withHeader returnes new instance
        $this->assertNotSame($message1, $this->messageMock);

        $this->assertTrue($message1->hasHeader('test'));
        $this->assertFalse($this->messageMock->hasHeader('test'));

        $this->assertEquals(array('test'), $message1->getHeader('test'));

        // Test if adding the same header value again, the same object is returned
        $message2 = $message1->withHeader('test', 'test');
        $this->assertSame($message1, $message2);

        $message3 = $message1->withAddedHeader('test', 'test2');

        // Check if withAddedHeader returnes new instance
        $this->assertNotSame($message1, $message3);

        // Test values of header
        $this->assertEquals(array('test', 'test2'), $message3->getHeader('test'));
        $this->assertEquals('test,test2', $message3->getHeaderLine('test'));
        $this->assertEquals(array('test' => array('test', 'test2')), $message3->getHeaders());

        $message4 = $message3->withoutHeader('test');
        $this->assertNotSame($message4, $message3);
    }

    /**
     * Test with protocol version
     *
     * @return void
     */
    public function testWithProtocolVersion()
    {
        $message1 = $this->messageMock->withProtocolVersion('1.0');
        $this->assertNotSame($message1, $this->messageMock);

        $this->assertEquals('1.1', $this->messageMock->getProtocolVersion());
        $this->assertEquals('1.0', $message1->getProtocolVersion());

        $message2 = $this->messageMock->withProtocolVersion('1.1');
        $this->assertSame($message2, $this->messageMock);
    }
}
