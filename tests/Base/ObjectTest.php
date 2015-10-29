<?php
namespace Neat\Test\Object;

use Neat\Test\Base\Fixture\Object\DefaultObject;
use Neat\Test\Base\Fixture\Object\ReadonlyObject;

class ObjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @return Object
     */
    public function object_setProperty_existingProperty()
    {
        $object = new DefaultObject;
        $object->property1 = 'property1_value';
        $object->property2 = 'property2_value';

        $this->assertSame('property1_value', $object->property1);
        $this->assertSame('property2_value', $object->property2);

        return $object;
    }

    /**
     * @test
     * @depends object_setProperty_existingProperty
     * @param DefaultObject $object
     */
    public function object_setProperty_overridesExistingProperty(DefaultObject $object)
    {
        $object->property1 = 'override_property1_value';
        $object->property2 = 'override_property2_value';

        $this->assertSame('override_property1_value', $object->property1);
        $this->assertSame('override_property2_value', $object->property2);
    }

    /**
     * @test
     * @depends object_setProperty_existingProperty
     * @expectedException \Neat\Data\Exception\OverflowException
     * @param DefaultObject $object
     */
    public function object_setProperty_nonExistingProperty_throwsException(DefaultObject $object)
    {
        $object->property3 = 'value';
    }

    /**
     * @test
     * @return ReadonlyObject
     */
    public function readonlyObject_setProperty_existingProperty()
    {
        $object = new ReadonlyObject;
        $object->property1 = 'property1_value';
        $object->property2 = 'property2_value';
        $object->property3 = 'property3_value';

        $this->assertSame('property1_value', $object->property1);
        $this->assertSame('property2_value', $object->property2);
        $this->assertSame('property3_value', $object->property3);

        return $object;
    }

    /**
     * @test
     * @depends readonlyObject_setProperty_existingProperty
     * @expectedException \Neat\Data\Exception\ReadonlyException
     * @param ReadonlyObject $object
     */
    public function readonlyObject_setProperty_overridesExistingProperty_throwsException(ReadonlyObject $object)
    {
        $object->property3 = 'property3_value';
        $object->property3 = 'override_property3_value';
    }
}