<?php
namespace Neat\Test\Router;

use Mockery;
use Mockery\Mock;
use Neat\Data\Data;
use Neat\Http\Request;
use Neat\Router\Router;
use Neat\Test\Base\AbstractComponentTest;

class RouterTest extends AbstractComponentTest
{
    /** @var Router */
    protected $subject;

    /** @var Mock|Request */
    protected $mockedRequest;

    /** @var Mock|Data */
    protected $mockedData;

    protected function setUp()
    {
        $this->mockedRequest = Mockery::mock('Neat\Http\Request');
        $this->mockedData = Mockery::mock('Neat\Data\Data');

        $this->subject = new Router;
        $this->subject->setRoutes([
            'test' => [
                'pattern' => '/name/:first/:last',
                'defaultValues' => [
                    'first' => 'first',
                    'last' => 'last',
                ],
                'requiredParams' => ['first', 'last'],
                'httpMethods' => ['POST'],
            ]
        ]);
        $this->subject->request = $this->mockedRequest;

        parent::setUp();
    }

    /**
     * @test
     */
    public function addRoute_string()
    {
        $this->subject->addRoute('/name/:first/:last');
        $this->assertSame('/name/:first/:last', $this->subject->getRoute('test')->getPattern());
    }

    /**
     * @test
     * @expectedException \Neat\Data\Exception\OverflowException
     */
    public function addRoute_array_throwsException()
    {
        $setting = [
            'pattern' => '/name/:first/:last',
            'non_existing_param' => 'test',
        ];

        $this->subject->addRoute($setting);
    }

    /**
     * @test
     */
    public function getRoute_existingRoute()
    {
        $this->assertInstanceOf('Neat\Router\Route', $this->subject->getRoute('test'));
        $this->assertSame('/name/:first/:last', $this->subject->getRoute('test')->getPattern());
        $this->assertFalse($this->subject->getRoute('test')->getHttpMethods()->get('GET'));
        $this->assertTrue($this->subject->getRoute('test')->getHttpMethods()->get('POST'));
    }

    /**
     * @test
     * @expectedException \Neat\Router\Exception\OutOfBoundsException
     */
    public function getRoute_nonExistingRoute_throwsException()
    {
        $this->subject->getRoute('non_existing_route');
    }

    /**
     * @test
     */
    public function route()
    {
        $routeEventValues = ['request' => $this->mockedRequest];
        $matchEventValues = ['route' => $this->subject->getRoute('test')];

        $this->mockedEventDispatcher
            ->shouldReceive('dispatchEvent')
            ->with('router.pre_route', $routeEventValues, $this->subject)
            ->andReturn($this->mockedEvent);

        $this->mockedEventDispatcher
            ->shouldReceive('dispatchEvent')
            ->with('router.post_route', $routeEventValues, $this->subject)
            ->andReturn($this->mockedEvent);

        $this->mockedEventDispatcher
            ->shouldReceive('dispatchEvent')
            ->with('router.pre_match', $matchEventValues, $this->subject)
            ->andReturn($this->mockedEvent);

        $this->mockedEventDispatcher
            ->shouldReceive('dispatchEvent')
            ->with('router.post_match', $matchEventValues, $this->subject)
            ->andReturn($this->mockedEvent);

        $this->mockedRequest
            ->shouldReceive('getProperties')
            ->andReturn($this->mockedData)
            ->shouldReceive('getPathInfo')
            ->andReturn('/name/first/last');

        $this->mockedData
            ->shouldReceive('get')
            ->andReturn($this->mockedData)
            ->shouldReceive('setValues');

        $this->assertInstanceOf('Neat\Http\Request', $this->subject->route());
    }
}