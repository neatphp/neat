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

            Definition::object(Service3::class)->setProperty1()->setProperty2(),

            'service1' => Definition::singleton(Service1::class)->properties(true),

            'service2' => Definition::singleton(Service2::class, '@service3', new Service4),

            'service3' => function () { return new Service3; },

            'service4' => Definition::singleton(Service4::class)->setProperty('@service5'),

            'service5' => Definition::singleton(Service5::class)->setProperty('@service6'),

            'service6' => Definition::singleton(Service6::class)->setProperty('@service4:getProperty'),
        ]);
    }

    /**
     * @test
     */
    public function get_existingIdAndUnique_returnsSingleton()
    {
        $service = $this->subject->get('service1');

        $this->assertInstanceOf(Service1::class, $service);
        $this->assertInstanceOf(Service2::class, $service->property1);
        $this->assertInstanceOf(Service3::class, $service->property2);
        $this->assertInstanceOf(Service4::class, $service->property3);
        $this->assertNull($service->property4);
        $this->assertNull($service->property5);
        $this->assertSame($service, $this->subject->get('service1'));
    }

    /**
     * @test
     */
    public function get_existingIdAndFactory_returnsSingleton()
    {
        $service = $this->subject->get('service2');

        $this->assertInstanceOf(Service2::class, $service);
        $this->assertInstanceOf(Service3::class, $service->getProperty1());
        $this->assertInstanceOf(Service4::class, $service->getProperty2());
        $this->assertSame($service, $this->subject->get('service2'));
    }

    /**
     * @test
     */
    public function get_existingClassAsIdAndUnique_returnsSingleton()
    {
        $service = $this->subject->get(Service1::class);

        $this->assertInstanceOf(Service1::class, $service);
        $this->assertNull($service->property1);
        $this->assertNull($service->property2);
        $this->assertNull($service->property4);
        $this->assertNull($service->property5);
        $this->assertSame($service, $this->subject->get(Service1::class));
    }

    /**
     * @test
     */
    public function get_existingClassAsIdAndNotUnique_returnsSingleton()
    {
        $service = $this->subject->get(Service2::class);

        $this->assertInstanceOf(Service2::class, $service);
        $this->assertNull($service->getProperty1());
        $this->assertNull($service->getProperty2());
        $this->assertSame($service, $this->subject->get(Service2::class));
    }

    /**
     * @test
     */
    public function get_existingIdAndMethodInjections_returnObject()
    {
        $service = $this->subject->get(Service3::class);

        $this->assertInstanceOf(Service3::class, $service);
        $this->assertInstanceOf(Service4::class, $service->getProperty1());
        $this->assertInstanceOf(Service5::class, $service->getProperty2());
        $this->assertNotSame($service, $this->subject->get(Service3::class));
    }

    /**
     * @test
     */
    public function get_nonExistingIdAndExistingClass_returnObject()
    {
        $service = $this->subject->get(Service4::class);

        $this->assertInstanceOf(Service4::class, $service);
        $this->assertNull($service->getProperty());
        $this->assertNotSame($service, $this->subject->get(Service4::class));
    }

    /**
     * @test
     * @expectedException \Neat\Container\Exception\UnexpectedValueException
     */
    public function get_emptyId_throwsException()
    {
        $this->subject->get('');
    }

    /**
     * @test
     * @expectedException \Neat\Container\Exception\OutOfBoundsException
     */
    public function get_nonExistingIdAndNonExistingClass_throwsException()
    {
        $this->subject->get('test');
    }

    /**
     * @test
     * @expectedException \Neat\Container\Exception\CircularDependencyException
     */
    public function get_circularDependency_throwException()
    {
        $this->subject->get('service6');
    }

    /**
     * @test
     * @expectedException \Neat\Container\Exception\InvalidArgumentException
     */
    public function set_existingIdAndInvalidValue_throwsException()
    {
        $this->subject->set('service2', 'test');
    }

    /**
     * @test
     * @expectedException \Neat\Container\Exception\ReadonlyException
     */
    public function set_existingIdAndReadonly_throwsException()
    {
        $this->subject->get(Service1::class);
        $this->subject->set(Service1::class, Definition::object(Service2::class));
    }

    /**
     * @test
     */
    public function set_nonExistingIdAndValidValue()
    {
        $this->subject->set('service', Definition::object(Service1::class));
        $this->assertInstanceOf(Service1::class, $this->subject->get('service'));
    }

    /**
     * @test
     * @expectedException \Neat\Container\Exception\InvalidArgumentException
     */
    public function set_nonExistingIdAndInvalidValue_throwsException()
    {
        $this->subject->set('service', 'test');
    }
}
