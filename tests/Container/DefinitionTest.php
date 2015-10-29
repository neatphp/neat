<?php
namespace Neat\Test\Container;

use Neat\Container\Definition;
use Neat\Test\Container\Fixture\Service1;
use Neat\Test\Container\Fixture\Service2;
use Neat\Test\Container\Fixture\Service3;
use Neat\Test\Container\Fixture\Service4;

class DefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function getClass_returnsReflectionClass()
    {
        $definition = Definition::object(Service1::class);
        $this->assertInstanceOf('ReflectionClass', $definition->getClass());
        $this->assertSame(Service1::class, $definition->getClass()->getName());
    }

    /**
     * @test
     */
    public function getPropertyInjections_returnsArray()
    {
        $definition = Definition::object(Service1::class)->properties(true, 'property5', 'test');
        $injections = $definition->getPropertyInjections();

        $this->assertInternalType('array', $injections);
        $this->assertSame(5, count($injections));
        $this->assertSame('@' . Service2::class, $injections['property1']);
        $this->assertSame('@' . Service3::class, $injections['property2']);
        $this->assertSame('@' . Service4::class, $injections['property3']);
        $this->assertNull($injections['property4']);
        $this->assertSame('test', $injections['property5']);
    }

    /**
     * @test
     */
    public function getConstructorInjections_returnsArray()
    {
        $definition = Definition::object(Service2::class, null, 'test');
        $injections = $definition->getConstructorInjections();

        $this->assertInternalType('array', $injections);
        $this->assertSame(2, count($injections));
        $this->assertSame('@' . Service3::class, $injections['param1']);
        $this->assertSame('test', $injections['param2']);
    }

    /**
     * @test
     */
    public function getMethodInjections_returnsArray()
    {
        $definition = Definition::object(Service3::class)
            ->methods('setProperty1')
            ->setProperty2('test');
        $injections = $definition->getMethodInjections();

        $this->assertInternalType('array', $injections);
        $this->assertSame(2, count($injections));
        $this->assertSame('@' . Service4::class, $injections['setProperty1'][0]['param']);
        $this->assertSame('test', $injections['setProperty2'][0]['param']);
    }

    /**
     * @test
     * @expectedException \Neat\Container\Exception\UnexpectedValueException
     */
    public function properties_AnnotationEnabledAndNonExistingProperty_throwsException()
    {
        Definition::object(Service1::class)->properties(true, 'property6', 'test');
    }

    /**
     * @test
     * @expectedException \Neat\Container\Exception\UnexpectedValueException
     */
    public function properties_AnnotationNotEnabledAndNonExistingProperty_throwsException()
    {
        Definition::object(Service1::class)->properties(false, 'property3', 'test');
    }
}