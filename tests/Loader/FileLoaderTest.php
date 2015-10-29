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
        $this->subject->setLocation($this->domain, [
            __DIR__ . '/Fixture/dir3',
            __DIR__ . '/Fixture/dir2',
            __DIR__ . '/Fixture/dir1',
        ]);
    }

    /**
     * @test
     */
    public function hasLocation_returnTrue()
    {
        $this->assertTrue($this->subject->hasLocation($this->domain));
    }

    /**
     * @test
     */
    public function hasLocation_returnFalse()
    {
        $this->assertFalse($this->subject->hasLocation('domain'));
    }

    /**
     * @test
     */
    public function getLocation_existingDomain_returnsArray()
    {
        $location = $this->subject->getLocation($this->domain);
        $this->assertInternalType('array', $location);
    }

    /**
     * @test
     * @expectedException \Neat\Loader\Exception\UnexpectedValueException
     */
    public function getLocation_emptyDomain_throwsException()
    {
        $this->subject->getLocation('');
    }

    /**
     * @test
     * @expectedException \Neat\Loader\Exception\OutOfBoundsException
     */
    public function getLocation_nonExistingDomain_throwsException()
    {
        $this->subject->getLocation('non_existing_domain');
    }

    /**
     * @test
     */
    public function getLocations_returnsArray()
    {
        $locations = $this->subject->getLocations();
        $this->assertInternalType('array', $locations);
    }

    /**
     * @test
     */
    public function locate()
    {
        $this->assertInternalType('string', $this->subject->locate('test.txt', $this->domain));
        $this->assertFalse($this->subject->locate('test.php', $this->domain));
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
