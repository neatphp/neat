<?php
namespace Neat\Test\Loader;

use Mockery;
use Mockery\Mock;
use Neat\Http\Request;
use Neat\Loader\TemplateLoader;

class TemplateLoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var TemplateLoader */
    private $subject;

    /** @var Mock|Request */
    private $mockedRequest;

    /** @var string */
    private $domain = 'test_domain';

    protected function setUp()
    {
        $this->mockedRequest = Mockery::mock('Neat\Http\Request');
        $this->subject = new TemplateLoader(__DIR__, $this->mockedRequest);
        $this->subject
            ->setLocation('*', ['{{module}}/View/{{controller}}'])
            ->setLocation($this->domain, ['{{module}}/View/{{controller}}']);
    }

    /**
     * @test
     */
    public function getLocation()
    {
        $this->mockedRequest
            ->shouldReceive('get')
            ->with('module')
            ->andReturn('TestModule')
            ->shouldReceive('get')
            ->with('controller')
            ->andReturn('TestController');

        $location = $this->subject->getLocation('non_existing_domain');
        $this->assertInternalType('array', $location);
        $this->assertSame(__DIR__ . '/TestModule/View/TestController', $location[0]);
    }

    /**
     * @test
     */
    public function getLocations()
    {
        $this->mockedRequest
            ->shouldReceive('get')
            ->with('module')
            ->andReturn('TestModule')
            ->shouldReceive('get')
            ->with('controller')
            ->andReturn('TestController');

        $locations = $this->subject->getLocations();
        $this->assertInternalType('array', $locations);
        $this->assertSame(__DIR__ . '/TestModule/View/TestController', $locations[$this->domain][0]);
    }
}
