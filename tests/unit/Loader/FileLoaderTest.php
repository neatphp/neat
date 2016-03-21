<?php
namespace Neat\Test\Loader;

use Neat\Loader\FileLoader;

class FileLoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var FileLoader */
    private $subject;

    /** @var string */
    private $domain = 'test_domain';

    protected function setUp()
    {
        $this->subject = new FileLoader;
        $this->subject
            ->setPlaceholders([
                'basedir' => __DIR__
            ])
            ->setLocations([
                $this->domain => [
                    '{{basedir}}/Fixture/dir3',
                    '{{basedir}}/Fixture/dir2',
                    '{{basedir}}/Fixture/dir1',
                ]
            ]);
    }

    /**
     * @test
     */
    public function locate_existingDomain()
    {
        $this->assertInternalType('string', $this->subject->locate('test.txt', $this->domain));
        $this->assertFalse($this->subject->locate('test.php', $this->domain));
    }

    /**
     * @test
     * @expectedException \Neat\Loader\Exception\UnexpectedValueException
     */
    public function locate_emptyDomain()
    {
        $this->subject->locate('test.php', '');
    }

    /**
     * @test
     * @expectedException \Neat\Loader\Exception\OutOfBoundsException
     */
    public function locate_nonExistingDomain()
    {
        $this->subject->locate('test.php', 'non_existing_domain');
    }

    /**
     * @test
     */
    public function load_existingFile()
    {
        $this->assertSame('dir3_test', $this->subject->load('test.txt', $this->domain));
    }

    /**
     * @test
     * @expectedException \Neat\Loader\Exception\UnexpectedValueException
     */
    public function load_nonExistingFile_throwsException()
    {
        $this->assertSame('dir3_test', $this->subject->load('test.php', $this->domain));
    }
}
