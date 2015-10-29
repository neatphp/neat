<?php
namespace Neat\Test\Parser;

use Neat\Parser\Csv;

class CsvTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function parse_withoutQuote_returnsArray()
    {
        $parser = new Csv('');
        $string = file_get_contents(__DIR__ . '/Fixture/csv/without_quote.csv');
        $array = require __DIR__ . '/Fixture/csv/array.php';
        $this->assertSame($array, $parser->parse($string));
    }

    /**
     * @test
     */
    public function parse_withQuote_returnsArray()
    {
        $parser = new Csv;
        $string = file_get_contents(__DIR__ . '/Fixture/csv/with_quote_extra_spaces.csv');
        $array = require __DIR__ . '/Fixture/csv/array.php';
        $this->assertSame($array, $parser->parse($string));
    }

    /**
     * @test
     * @expectedException \Neat\Parser\Exception\UnexpectedValueException
     */
    public function parse_withoutQuoteAtStart_throwsException()
    {
        $parser = new Csv;
        $string = file_get_contents(__DIR__ . '/Fixture/csv/without_quote_at_start.csv');
        $parser->parse($string);
    }

    /**
     * @test
     * @expectedException \Neat\Parser\Exception\UnexpectedValueException
     */
    public function parse_withoutQuoteAtEnd_throwsException()
    {
        $parser = new Csv;
        $string = file_get_contents(__DIR__ . '/Fixture/csv/without_quote_at_end.csv');
        $parser->parse($string);
    }

    /**
     * @test
     */
    public function dump_returnsString()
    {
        $parser = new Csv;
        $string = file_get_contents(__DIR__ . '/Fixture/csv/with_quote.csv');
        $array = require __DIR__ . '/Fixture/csv/array.php';
        $this->assertSame($string, $parser->dump($array));
    }
}
