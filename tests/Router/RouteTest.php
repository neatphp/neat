<?php
namespace Neat\Test\Router;

use Neat\Data\Helper\Validator;
use Neat\Router\Route;
use Neat\Data\Data;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @return Route
     */
    public function match_withoutWildcard_returnTrue()
    {
        $route = new Route('/name/:first/:last');
        $this->assertTrue($route->match('/name/first/last'));

        return $route;
    }

    /**
     * @test
     */
    public function match_withoutWildcard_returnFalse()
    {
        $route = new Route('/name/:first/:last');
        $this->assertFalse($route->match('/name'));
    }

    /**
     * @test
     * @return Route
     */
    public function match_withWildcard_returnTrue()
    {
        $route = new Route('/name/:name+/date/:date+/time/:time+');
        $this->assertTrue($route->match('/name/first/last/date/year/month/day/time/hour/minute/second'));

        return $route;
    }

    /**
     * @test
     */
    public function match_withWildcard_returnFalse()
    {
        $route = new Route('/name/:name+/date/:date+');
        $this->assertFalse($route->match('/name/first/last'));
    }

    /**
     * @test
     */
    public function match_withOptionalParameters_returnTrue()
    {
        $route = new Route('/date/:year(/:month(/:day))');
        $this->assertTrue($route->match('/date/2000'));
        $this->assertTrue($route->match('/date/2000/01'));
        $this->assertTrue($route->match('/date/2000/01/01'));
    }

    /**
     * @test
     */
    public function match_withOptionalParametersAndWildcard_returnTrue()
    {
        $route = new Route('/date/:year/:month/:day(/:time+)');
        $this->assertTrue($route->match('/date/2000/01/01'));
        $this->assertTrue($route->match('/date/2000/01/01/12/0/0'));
    }

    /**
     * @test
     */
    public function match_withValidator_returnFalse()
    {
        $route = new Route('/name/:first/:last');
        $route->getUrlParams()->setValidator(new Validator([
            'first' => function () { return false; }
        ]));

        $this->assertFalse($route->match('/name/first/last'));
        $this->assertNotEmpty($route->getError());
    }

    /**
     * @test
     * @depends match_withoutWildcard_returnTrue
     * @param Route $route
     * @return Data
     */
    public function getUrlParams_withoutWildcard_returnData(Route $route)
    {
        $params = $route->getUrlParams();
        $this->assertEquals(2, count($params->toArray()));
        $this->assertSame(['first' => 'first', 'last' => 'last'], $params->toArray());

        return $params;
    }

    /**
     * @test
     * @depends match_withWildcard_returnTrue
     * @param Route $route
     * @return Data
     */
    public function getUrlParams_withWildcard_returnData(Route $route)
    {
        $params = $route->getUrlParams();
        $this->assertEquals(3, count($params->toArray()));
        $this->assertSame([
            'name' => ['first', 'last'],
            'date' => ['year', 'month', 'day'],
            'time' => ['hour', 'minute', 'second'],
        ], $params->toArray());

        return $params;
    }

    /**
     * @test
     * @depends getUrlParams_withoutWildcard_returnData
     * @param Data $params
     */
    public function data_accessExistingParam(Data $params)
    {
        $this->assertEquals('first', $params['first']);
        $this->assertEquals('last', $params['last']);
    }

    /**
     * @test
     * @depends getUrlParams_withoutWildcard_returnData
     * @expectedException \Neat\Data\Exception\OutOfBoundsException
     * @param Data $params
     */
    public function data_accessNonExistingParam(Data $params)
    {
        $params['none_existing_param'];
    }

    /**
     * @test
     */
    public function assemble_withoutWildcard_returnString()
    {
        $route = new Route('/name/:first/:last');
        $this->assertSame('/name/first/last', $route->assemble([
            'first' => 'first',
            'last' => 'last',
        ]));
    }

    /**
     * @test
     */
    public function assemble_withWildcard_returnString()
    {
        $route = new Route('/name/:name+/date/:date+/time/:time+');
        $this->assertSame('/name/name/date/date/time/time', $route->assemble([
            'name' => 'name',
            'date' => 'date',
            'time' => 'time',
        ]));
    }

    /**
     * @test
     */
    public function assemble_withOptionalParameters_returnString()
    {
        $route = new Route('/date/:year(/:month(/:day))');
        $this->assertSame('/date/year/month/day', $route->assemble([
            'year' => 'year',
            'month' => 'month',
            'day' => 'day',
        ]));
        $this->assertSame('/date/year/month', $route->assemble([
            'year' => 'year',
            'month' => 'month',
        ]));
    }

    /**
     * @test
     */
    public function assemble_withOptionalParametersAndWildcard_returnString()
    {
        $route = new Route('/date/:year/:month/:day(/:time+)');
        $this->assertSame('/date/year/month/day/time', $route->assemble([
            'year' => 'year',
            'month' => 'month',
            'day' => 'day',
            'time' => 'time',
        ]));
        $this->assertSame('/date/year/month/day', $route->assemble([
            'year' => 'year',
            'month' => 'month',
            'day' => 'day',
        ]));
    }

    /**
     * @test
     * @expectedException \Neat\Router\Exception\UnexpectedValueException
     */
    public function assemble_withEmptyParameter_throwsException()
    {
        $route = new Route('/date/:year/:month/:day(/:time+)');
        $route->assemble([
            'year' => 'year',
            'month' => 'month',
        ]);
    }
}