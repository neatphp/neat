<?php
namespace Neat\Test\Parser;

use Neat\Parser\Json;

class JsonTest extends \PHPUnit_Framework_TestCase
{
    /** @var Json */
    private $subject;

    /** @var string */
    private $string = '{"key1":"value1","key2":"value2","key3":"value3"}';

    /** @var array */
    private $array = [
        'key1' => 'value1',
        'key2' => 'value2',
        'key3' => 'value3',
    ];

    public function setUp()
    {
        $this->subject = new Json;
    }

    /**
     * @test
     */
    public function parse_returnsArray()
    {

        $this->assertSame($this->array, $this->subject->parse($this->string));
    }

    /**
     * @test
     */
    public function dump_returnsString()
    {
        $this->assertSame($this->string, $this->subject->dump($this->array));
    }

    /**
     * @test
     */
    public function getError_returnsString()
    {
        $this->subject->parse($this->string);
        $this->assertSame('No error', $this->subject->getError());
        $this->subject->parse('test');
        $this->assertSame('Syntax error', $this->subject->getError());
    }
}
