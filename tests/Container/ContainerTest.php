<?php
namespace Neat\Test\Container;

use Neat\Container\Container;
use Neat\Container\Definition;
use Neat\Test\Container\Fixture\Service1;
use Neat\Test\Container\Fixture\Service2;
use Neat\Test\Container\Fixture\Service3;
use Neat\Test\Container\Fixture\Service4;
use Neat\Test\Container\Fixture\Service5;
use Neat\Test\Container\Fixture\Service6;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Container */
    private $subject;

    protected function setUp()
    {
        $this->subject = new Container([
            Service1::class,

            new Service2,

            Definition::object(Service3::class)
                ->method('setProperty1')
                ->method('setProperty2'),

            'service1' => Definition::singleton(Service1::class),

            'service2' => Definition::singleton(Service2::class, '@service3', new Service4),

            'service3' => function () {
                return new Service3;
            },

            'service4' => Definition::singleton(Service4::class)
                ->method('setProperty', '@service5'),

            'service5' => Definition::singleton(Service5::class)
                ->method('setProperty', '@service6'),

            'service6' => Definition::singleton(Service6::class)
                ->method('setProperty', '@service4:getProperty'),
        ]);
    }

    public function testGet_withExistingIdAndUnique_returnsSingleton()
    {
        $service = $this->subject->get('service1');

        $this->assertSame($service, $this->subject->get('service1'));
        $this->assertInstanceOf(Service1::class, $service);
        $this->assertInstanceOf(Service2::class, $service->property1);
        $this->assertInstanceOf(Service3::class, $service->property2);
        $this->assertInstanceOf(Service4::class, $service->property3);
        $this->assertNull($service->property4);
        $this->assertNull($service->property5);
    }

    public function testGet_withExistingIdAndFactory_returnsSingleton()
    {
        $service = $this->subject->get('service2');
#
        $this->assertSame($service, $this->subject->get('service2'));
        $this->assertInstanceOf(Service2::class, $service);
        $this->assertInstanceOf(Service3::class, $service->getProperty1());
        $this->assertInstanceOf(Service4::class, $service->getProperty2());
    }

    public function testGet_withExistingClassAsIdAndUnique_returnsSingleton()
    {
        $service = $this->subject->get(Service1::class);

        $this->assertSame($service, $this->subject->get(Service1::class));
        $this->assertInstanceOf(Service1::class, $service);
        $this->assertInstanceOf(Service1::class, $service);
        $this->assertInstanceOf(Service2::class, $service->property1);
        $this->assertInstanceOf(Service3::class, $service->property2);
        $this->assertInstanceOf(Service4::class, $service->property3);
        $this->assertNull($service->property4);
        $this->assertNull($service->property5);
    }

    public function testGet_withExistingClassAsIdAndNotUnique_returnsSingleton()
    {
        $service = $this->subject->get(Service2::class);

        $this->assertSame($service, $this->subject->get(Service2::class));
        $this->assertInstanceOf(Service2::class, $service);
        $this->assertNull($service->getProperty1());
        $this->assertNull($service->getProperty2());
    }

    public function testGet_withExistingIdAndMethodInjections_returnsObject()
    {
        $service = $this->subject->get(Service3::class);

        $this->assertNotSame($service, $this->subject->get(Service3::class));
        $this->assertInstanceOf(Service3::class, $service);
        $this->assertInstanceOf(Service4::class, $service->getProperty1());
        $this->assertInstanceOf(Service5::class, $service->getProperty2());
    }

    public function testGet_withNonExistingIdAndExistingClass_returnsObject()
    {
        $service = $this->subject->get(Service4::class);

        $this->assertNotSame($service, $this->subject->get(Service4::class));
        $this->assertInstanceOf(Service4::class, $service);
        $this->assertNull($service->getProperty());
    }

    public function testGet_withEmptyId_throwsException()
    {
        $this->setExpectedException('Neat\Container\Exception\UnexpectedValueException');
        $this->subject->get('');
    }

    public function testGet_withNonExistingIdAndNonExistingClass_throwsException()
    {
        $this->setExpectedException('Neat\Container\Exception\OutOfBoundsException');
        $this->subject->get('test');
    }

    public function testGet_withCircularDependency_throwsException()
    {
        $this->setExpectedException('Neat\Container\Exception\CircularDependencyException');
        $this->subject->get('service6');
    }

    public function testSet_withNonExistingIdAndValidValue()
    {
        $this->subject->set('service', Definition::object(Service1::class));
        $this->assertInstanceOf(Service1::class, $this->subject->get('service'));
    }

    public function testSet_withNonExistingIdAndInvalidValue_throwsException()
    {
        $this->setExpectedException('Neat\Container\Exception\InvalidArgumentException');
        $this->subject->set('service', 'test');
    }

    public function testSet_withExistingIdAndInvalidValue_throwsException()
    {
        $this->setExpectedException('Neat\Container\Exception\InvalidArgumentException');
        $this->subject->set('service2', 'test');
    }

    public function testSet_withExistingIdAndReadonly_throwsException()
    {
        $this->subject->get(Service1::class);
        $this->setExpectedException('Neat\Container\Exception\ReadonlyException');
        $this->subject->set(Service1::class, Definition::object(Service2::class));
    }
}
