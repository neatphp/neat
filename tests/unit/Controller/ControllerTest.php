<?php
namespace Neat\Test\Controller;

use Mockery;
use Mockery\Mock;
use Neat\Http\Request;
use Neat\Loader\PluginLoader;
use Neat\Test\Base\AbstractComponentTest;
use Neat\Test\Controller\Fixture\Controller;

class ControllerTest extends AbstractComponentTest
{
    /** @var Controller */
    protected $subject;

    /** @var Mock|Request */
    protected $mockedRequest;

    /** @var Mock|FileLoader */
    protected $mockedFileLoader;

    /** @var Mock|PluginLoader */
    protected $mockedPluginLoader;

    protected function setUp()
    {
        $this->mockedRequest = Mockery::mock('Neat\Http\Request');
        $this->mockedFileLoader = Mockery::mock('Neat\Loader\FileLoader');
        $this->mockedPluginLoader = Mockery::mock('Neat\Loader\PluginLoader');

        $this->subject = new Controller;
        $this->subject->request = $this->mockedRequest;
        $this->subject->fileLoader = $this->mockedFileLoader;
        $this->subject->pluginLoader = $this->mockedPluginLoader;

        parent::setUp();
    }

    /**
     * @test
     */
    public function execute_returnsResponse()
    {
        $values = ['action' => 'default'];

        $this->mockedEventDispatcher
            ->shouldReceive('dispatchEvent')
            ->with('controller.pre_execute', $values, $this->subject)
            ->andReturn($this->mockedEvent);

        $this->mockedEventDispatcher
            ->shouldReceive('dispatchEvent')
            ->with('controller.post_execute', $values, $this->subject)
            ->andReturn($this->mockedEvent);

        $this->mockedEvent
            ->shouldReceive('setValues')
            ->with($values);

        $this->mockedEvent
            ->shouldReceive('offsetGet')
            ->with('action')
            ->andReturn('default');

        $response = $this->subject->execute('default');
        $this->assertInstanceOf('Neat\Http\Response', $response);
    }

    /**
     * @test
     */
    public function getTemplate_returnsString()
    {
        $this->mockedRequest
            ->shouldReceive('get')
            ->with('action')
            ->andReturn('default');

        $this->assertSame('default.html', $this->invokeMethod('getTemplate'));
    }

    /**
     * @test
     */
    public function getPlugin_returnObject()
    {
        $this->mockedPluginLoader
            ->shouldReceive('load')
            ->with('name', 'superclass')
            ->andReturn('stdClass');

        $this->assertInstanceOf('stdClass', $this->invokeMethod('getPlugin', ['name', 'superclass']));
    }

    /**
     * @test
     */
    public function render_withTemplateEngine_returnString()
    {
        $this->subject->setTemplateEngine(function () {
            return 'test';
        });

        $this->mockedFileLoader
            ->shouldReceive('locate')
            ->with('test', 'template')
            ->andReturn('path');

        $response = $this->invokeMethod('render', ['test', ['param1' => 'value1', 'param2' => 'value2']]);
        $this->assertInstanceOf('Neat\Http\Response', $response);
        $this->assertSame('test', $response->body);
    }

    /**
     * @test
     */
    public function render_withoutTemplateEngine_returnString()
    {
        $this->mockedFileLoader
            ->shouldReceive('load')
            ->with('test', 'template')
            ->andReturn('test {{param1}} test {{param2}} test');

        $response = $this->invokeMethod('render', ['test', ['param1' => 'value1', 'param2' => 'value2']]);
        $this->assertInstanceOf('Neat\Http\Response', $response);
        $this->assertSame('test value1 test value2 test', $response->body);
    }
}
