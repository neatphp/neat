<?php
namespace Neat\Test\Base;

use Mockery;
use Mockery\Mock;
use Neat\Base\Component;
use Neat\Config\Config;
use Neat\Event\Dispatcher;
use Neat\Event\Event;

abstract class AbstractComponentTest extends \PHPUnit_Framework_TestCase
{
    /** @var Component */
    protected $subject;

    /** @var Mock|Config */
    protected $mockedConfig;

    /** @var Mock|Dispatcher */
    protected $mockedEventDispatcher;

    /** @var Mock|Event */
    protected $mockedEvent;

    protected function setUp()
    {
        $this->mockedConfig = Mockery::mock('Neat\Config\Config');
        $this->mockedEventDispatcher = Mockery::mock('Neat\Event\Dispatcher');
        $this->mockedEvent = Mockery::mock('Neat\Event\Event');

        $this->subject->config = $this->mockedConfig;
        $this->subject->dispatcher = $this->mockedEventDispatcher;
    }

    /**
     * Invokes private/protected methods.
     *
     * @param string $methodName
     * @param string array $args
     * @return mixed
     */
    protected function invokeMethod($methodName, array $args = [])
    {
        $reflection = new \ReflectionClass(get_class($this->subject));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($this->subject, $args);
    }
}
