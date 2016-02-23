<?php
/*
 * Copyright (c) 2015 babymarkt.de GmbH - All Rights Reserved
 *
 * All information contained herein is, and remains the property of babymarkt.de GmbH
 * and is protected by copyright law. Unauthorized copying of this file or any parts,
 * via any medium is strictly prohibited.
 */

namespace Babymarkt\Insect\Cable\Tests\Unit\Communication\Message;

use Babymarkt\Insect\Cable\Communication\Message\StringStream;

/**
 * Class StringStreamTest
 *
 * @package Babymarkt\Insect\Cable\Tests\Unit\Communication\Message
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
