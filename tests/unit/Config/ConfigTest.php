<?php
namespace Neat\Test\Config;

use Mockery;
use Mockery\Mock;
use Neat\Config\Config;
use Neat\Loader\FileLoader;
use Neat\Parser\ParserInterface;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /** @var Config */
    private $subject;

    protected function setUp()
    {
        $settings = [
            'offset1' => '',
            'offset2' => 'offset2_value',
            'path1' => [
                'path1' => [
                    'path1' => '',
                ]
            ],
            'path2' => [
                'path2' => [
                    'path2' => 'path2_value',
                ]
            ],
        ];

        /** @var Mock|FileLoader $mockedFileLoader */
        $mockedFileLoader = Mockery::mock('Neat\Loader\FileLoader');
        $mockedFileLoader
            ->shouldReceive('load')
            ->with('file.ext', 'config')
            ->once()
            ->andReturn('file_content');

        /** @var Mock|ParserInterface $mockedParser */
        $mockedParser = Mockery::mock('Neat\Parser\ParserInterface');
        $mockedParser
            ->shouldReceive('parse')
            ->with('file_content')
            ->once()
            ->andReturn($settings);

        $this->subject = new Config($mockedFileLoader, $mockedParser);
        $this->subject
            ->setPlaceholders(['placeholder' => 'placeholder_value'])
            ->loadFile('file.ext');
    }

    public function testLoadFile()
    {
        $settings = [
            'offset1' => 'offset1_value',
            'path1' => [
                'path1' => [
                    'path1' => 'path1_value',
                ]
            ],
        ];

        /** @var Mock|FileLoader $mockedFileLoader */
        $mockedFileLoader = $this->subject->getFileLoader();
        $mockedFileLoader
            ->shouldReceive('load')
            ->with('file.ext', 'config')
            ->once()
            ->andReturn('file_content');

        /** @var Mock|ParserInterface $mockedParser */
        $mockedParser = $this->subject->getParser();
        $mockedParser
            ->shouldReceive('parse')
            ->with('file_content')
            ->once()
            ->andReturn($settings);

        $this->subject->loadFile('file.ext');

        $this->assertSame(['{{placeholder}}' => 'placeholder_value'], $this->subject->getPlaceholders());
        $this->assertSame('offset1_value', $this->subject->get('offset1'));
        $this->assertSame('path1_value', $this->subject->get('path1.path1.path1'));
    }

    public function testHas_existingOffset_returnsTrue()
    {
        $this->assertTrue($this->subject->has('offset1'));
        $this->assertTrue($this->subject->has('offset2'));
    }

    public function testHas_nonExistingOffset_returnsFalse()
    {
        $this->assertFalse($this->subject->has('offset'));
    }

    public function testHas_existingPath_returnsTrue()
    {
        $this->assertTrue($this->subject->has('path1.path1'));
        $this->assertTrue($this->subject->has('path1.path1.path1'));
        $this->assertTrue($this->subject->has('path2.path2'));
        $this->assertTrue($this->subject->has('path2.path2.path2'));
    }

    public function testHas_nonExistingPath_returnsFalse()
    {
        $this->assertFalse($this->subject->has('path.path.path'));
    }

    public function testGet_existingOffset()
    {
        $this->assertSame('', $this->subject->get('offset1'));
        $this->assertSame('offset2_value', $this->subject->get('offset2'));
    }

    public function testGet_existingPath()
    {
        $this->assertSame(['path1' => ''], $this->subject->get('path1.path1'));
        $this->assertSame('', $this->subject->get('path1.path1.path1'));
        $this->assertSame(['path2' => 'path2_value'], $this->subject->get('path2.path2'));
        $this->assertSame('path2_value', $this->subject->get('path2.path2.path2'));
    }

    public function testGet_invalidPath_throwsException()
    {
        $this->setExpectedException('Neat\Config\Exception\InvalidArgumentException');
        $this->subject->get([]);
    }

    public function testGet_pathSeparator_throwsException()
    {
        $this->setExpectedException('Neat\Config\Exception\UnexpectedValueException');
        $this->subject->get('.');
    }

    public function testGet_emptyPath_throwsException()
    {
        $this->setExpectedException('Neat\Config\Exception\UnexpectedValueException');
        $this->subject->get('');
    }

    public function testGet_nonExistingOffset_throwsException()
    {
        $this->setExpectedException('Neat\Config\Exception\OutOfBoundsException');
        $this->subject->get('offset');
    }

    public function testGet_nonExistingPath_throwsException()
    {
        $this->setExpectedException('Neat\Config\Exception\OutOfBoundsException');
        $this->subject->get('path.path.path');
    }
}
