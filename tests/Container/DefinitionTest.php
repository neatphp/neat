<?php
namespace Neat\Test\Container;

use Neat\Container\Definition;
use Neat\Test\Container\Fixture\Service1;
use Neat\Test\Container\Fixture\Service2;
use Neat\Test\Container\Fixture\Service3;
use Neat\Test\Container\Fixture\Service4;

class DefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetClass_returnsReflectionClass()
    {
        $definition = Definition::object(Service1::class);
        $this->assertInstanceOf('ReflectionClass', $definition->getClass());
        $this->assertSame(Service1::class, $definition->getClass()->getName());
    }

    public function testGetPropertyInjections_returnsArray()
    {
        $definition = Definition::object(Service1::class)->property('property5', 'test');
        $injections = $definition->getPropertyInjections();

        $this->assertInternalType('array', $injections);
        $this->assertSame(5, count($injections));
        $this->assertSame('@' . Service2::class, $injections['property1']);
        $this->assertSame('@' . Service3::class, $injections['property2']);
        $this->assertSame('@' . Service4::class, $injections['property3']);
        $this->assertNull($injections['property4']);
        $this->assertSame('test', $injections['property5']);
    }

    public function testGetConstructorInjections_returnsArray()
    {
        $definition = Definition::object(Service2::class, null, 'test');
        $injections = $definition->getConstructorInjections();

        $this->assertInternalType('array', $injections);
        $this->assertSame(2, count($injections));
        $this->assertSame('@' . Service3::class, $injections['param1']);
        $this->assertSame('test', $injections['param2']);
    }

    public function testGetMethodInjections_returnsArray()
    {
        $definition = Definition::object(Service3::class)
            ->method('setProperty1')
            ->method('setProperty2', 'test');
        $injections = $definition->getMethodInjections();

        $this->assertInternalType('array', $injections);
        $this->assertSame(2, count($injections));
        $this->assertSame('@' . Service4::class, $injections['setProperty1'][0]['param']);
        $this->assertSame('test', $injections['setProperty2'][0]['param']);
    }

    public function testProperty_withNonExistingProperty_throwsException()
    {
        $this->setExpectedException('Neat\Container\Exception\UnexpectedValueException');
        Definition::object(Service1::class)->property('property6', 'test');
    }
}