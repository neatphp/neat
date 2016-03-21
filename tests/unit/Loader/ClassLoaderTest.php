<?php
namespace Neat\Test\Loader;

use Neat\Loader\ClassLoader;

class ClassLoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var ClassLoader */
    private $subject;

    /** @var string */
    private $namespace = 'Neat\Test\Loader';

    protected function setUp()
    {
        $this->subject = new ClassLoader;
        $this->subject
            ->setClassMaps([
                'Neat\Test\Loader\Fixture\TestClass3' => __DIR__ . '/Fixture/TestClass3.php'
            ])
            ->setLocations([
                $this->namespace => __DIR__
            ]);
    }

    /**
     * @test
     */
    public function locate()
    {
        $this->assertInternalType('string', $this->subject->locate($this->namespace . '\Fixture\TestClass1', $this->namespace));
        $this->assertFalse($this->subject->locate($this->namespace . '\Fixture\TestClass', $this->namespace));
    }

    /**
     * @test
     */
    public function load()
    {
        $this->assertTrue($this->subject->load($this->namespace . '\Fixture\TestClass1', $this->namespace));
        $this->assertFalse($this->subject->load($this->namespace . '\Fixture\TestClass', $this->namespace));
    }

    /**
     * @test
     */
    public function autoload()
    {
        $this->assertTrue($this->subject->autoload($this->namespace . '\Fixture\TestClass2'));
        $this->assertTrue($this->subject->autoload($this->namespace . '\Fixture\TestClass3'));
        $this->assertFalse($this->subject->autoload($this->namespace . '\Fixture\TestClass'));
    }
}
