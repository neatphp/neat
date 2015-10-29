<?php
namespace Neat\Test\Loader;

use Mockery;
use Mockery\Mock;
use Neat\Http\Request;
use Neat\Loader\PluginLoader;

class PluginLoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var PluginLoader */
    private $subject;

    /** @var Mock|Request */
    private $mockedRequest;

    /** @var string */
    private $superclass = 'Neat\Test\Loader\Fixture\PluginInterface';

    protected function setUp()
    {
        $this->mockedRequest = Mockery::mock('Neat\Http\Request');
        $this->subject = new PluginLoader(__DIR__, $this->mockedRequest);
        $this->subject->setLocation($this->superclass, [
            'Fixture/{{module}}/Plugin',
            'Fixture/Plugin',
        ]);

        $this->mockedRequest
            ->shouldReceive('get')
            ->with('module')
            ->andReturn('Module');
    }

    /**
     * @test
     */
    public function locate_returnsString()
    {
        $plugin1Path = __DIR__ . '/Fixture/Plugin/Plugin1.php';
        $plugin2Path = __DIR__ . '/Fixture/Module/Plugin/Plugin2.php';
        $this->assertSame($plugin1Path, $this->subject->locate('plugin 1', $this->superclass));
        $this->assertSame($plugin2Path, $this->subject->locate('plugin 2', $this->superclass));
        $this->assertFalse($this->subject->locate('Plugin', $this->superclass));
    }

    /**
     * @test
     */
    public function locate_returnsFalse()
    {
        $this->assertFalse($this->subject->locate('test', $this->superclass));
    }

    /**
     * @test
     */
    public function load_existingPlugin()
    {
        $pluginClass = 'Neat\Test\Loader\Fixture\Plugin';
        $this->assertSame($pluginClass, $this->subject->load('plugin', $this->superclass));

        $plugin1Class = 'Neat\Test\Loader\Fixture\Plugin\Plugin1';
        $this->assertSame($plugin1Class, $this->subject->load('plugin 1', $this->superclass));

        $plugin2Class = 'Neat\Test\Loader\Fixture\Module\Plugin\Plugin2';
        $this->assertSame($plugin2Class, $this->subject->load('plugin 2', $this->superclass));
    }

    /**
     * @test
     * @expectedException \Neat\Loader\Exception\DomainException
     */
    public function load_existingFileAndNoPlugin()
    {
        $this->subject->load('plugin 3', $this->superclass);
    }

    /**
     * @test
     * @expectedException \Neat\Loader\Exception\UnexpectedValueException
     */
    public function load_existingFileAndNoClass()
    {
        $this->subject->load('plugin 4', $this->superclass);
    }
}
