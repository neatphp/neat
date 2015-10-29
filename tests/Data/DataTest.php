<?php
namespace Neat\Test\Data;

use Neat\Data\Data;
use Neat\Test\Data\Fixture\Filter;
use Neat\Test\Data\Fixture\Validator;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /** @var Data */
    private $subject;

    protected function setUp()
    {
        $this->subject = new Data(true, true);
        $this->subject
            ->loadOffsets(['offset1', 'offset2', 'offset3', 'path1', 'path2', 'path3'])
            ->requireOffsets(['offset1', 'offset2'])
            ->loadValues(['offset1' => 1])
            ->setFilter(new Filter)
            ->setValidator(new Validator)
            ->set('path1', function () {
                return new Data;
            })->set('path2', function () {
                $data = new Data;
                $data->loadOffsets(['path2'])->setValidator(new Validator);

                return $data;
            })->set('path3', function () {
                $data = new Data;
                $data->loadOffsets(['path3'])->set('path3.path3', 'path3_test');

                return $data;
            });
    }

    /**
     * @test
     */
    public function clone_objectIsNotSame()
    {
        $filter = $this->subject->getFilter();
        $validator = $this->subject->getValidator();
        $clone = clone $this->subject;

        $this->assertNotSame($filter, $clone->getFilter());
        $this->assertNotSame($validator, $clone->getValidator());
    }

    /**
     * @test
     */
    public function offsetExists_existingOffset_returnsTrue()
    {
        $this->assertTrue(isset($this->subject['offset1']));
    }

    /**
     * @test
     */
    public function offsetExists_existingOffsetAndEmpty_returnsFalse()
    {
        $this->assertFalse(isset($this->subject['offset2']));
        $this->assertFalse(isset($this->subject['offset3']));
    }

    /**
     * @test
     */
    public function offsetExists_nonExistingOffset_returnsFalse()
    {
        $this->assertFalse(isset($this->subject['Offset1']));
        $this->assertFalse(isset($this->subject['Offset2']));
        $this->assertFalse(isset($this->subject['Offset3']));
    }

    /**
     * @test
     */
    public function offsetGet_existingOffset_returnsScalar()
    {
        $this->assertSame(1, $this->subject['offset1']);
    }

    /**
     * @test
     */
    public function offsetGet_existingOffsetAndFilter_returnsScalar()
    {
        $this->subject['offset2'] = 'offset2';
        $this->assertSame('offset2 is filtrated', $this->subject['offset2']);
    }

    /**
     * @test
     */
    public function offsetGet_existingOffset_ReturnsLazyLoad()
    {
        $this->assertInstanceOf('Neat\Data\Data', $this->subject['path1']);
        $this->assertInstanceOf('Neat\Data\Data', $this->subject['path2']);
        $this->assertInstanceOf('Neat\Data\Data', $this->subject['path3']);
    }

    /**
     * @test
     * @expectedException \Neat\Data\Exception\OutOfBoundsException
     */
    public function offsetGet_nonExistingOffset_throwsException()
    {
        $this->subject['Offset1'];
    }

    /**
     * @test
     */
    public function offsetSet_existingOffsetAndValidValue()
    {
        $this->subject['offset3'] = [];
    }

    /**
     * @test
     * @expectedException \Neat\Data\Exception\InvalidArgumentException
     */
    public function offsetSet_existingOffsetAndInvalidValue_throwsException()
    {
        $this->subject['offset3'] = 'test';
    }

    /**
     * @test
     * @expectedException \Neat\Data\Exception\ReadonlyException
     */
    public function offsetSet_existingOffsetAndReadonly_throwsException()
    {
        $this->subject['offset1'] = 2;
    }

    /**
     * @test
     * @expectedException \Neat\Data\Exception\OverflowException
     */
    public function offsetSet_nonExistingOffset_throwsException()
    {
        $this->subject['Offset1'] = 'test';
    }

    /**
     * @test
     */
    public function offsetUnset_existingOffset()
    {
        unset($this->subject['offset1']);
        $this->assertFalse(isset($this->subject['offset1']));
    }

    /**
     * @test
     */
    public function offsetUnset_nonExistingOffset()
    {
        unset($this->subject['Offset1']);
        $this->assertFalse(isset($this->subject['Offset1']));
    }

    /**
     * @test
     */
    public function has_existingOffset_returnsTrue()
    {
        $this->assertTrue($this->subject->has('offset1'));
    }

    /**
     * @test
     */
    public function has_existingOffsetAndEmpty_returnsFalse()
    {
        $this->assertFalse($this->subject->has('offset2'));
        $this->assertFalse($this->subject->has('offset3'));
    }

    /**
     * @test
     */
    public function has_nonExistingOffset_returnsFalse()
    {
        $this->assertFalse($this->subject->has('Offset1'));
    }

    /**
     * @test
     */
    public function has_existingPath_returnsTrue()
    {
        $this->assertTrue($this->subject->has('path3.path3.path3'));
    }

    /**
     * @test
     */
    public function has_existingPathAndEmpty_returnsFalse()
    {
        $this->assertFalse($this->subject->has('path2.path2'));
    }

    /**
     * @test
     */
    public function has_nonExistingPath_returnsFalse()
    {
        $this->assertFalse($this->subject->has('path2.path2.path2'));
        $this->assertFalse($this->subject->has('path.path.path'));
    }

    /**
     * @test
     */
    public function get_existingOffset()
    {
        $this->assertSame(1, $this->subject->get('offset1'));
    }

    /**
     * @test
     */
    public function get_existingPath()
    {
        $this->assertNull($this->subject->get('path2.path2'));
        $this->assertSame('path3_test', $this->subject->get('path3.path3.path3'));
    }

    /**
     * @test
     * @expectedException \Neat\Data\Exception\UnexpectedValueException
     */
    public function get_rootPath_throwsException()
    {
        $this->subject->get('.');
    }

    /**
     * @test
     * @expectedException \Neat\Data\Exception\UnexpectedValueException
     */
    public function get_emptyPath_throwsException()
    {
        $this->subject->get('');
    }

    /**
     * @test
     * @expectedException \Neat\Data\Exception\OutOfBoundsException
     */
    public function get_nonExistingOffset_throwsException()
    {
        $this->subject->get('Offset1');
    }

    /**
     * @test
     * @expectedException \Neat\Data\Exception\OutOfBoundsException
     */
    public function get_nonExistingPath_throwsException()
    {
        $this->subject->get('path2.path2.path2');
    }

    /**
 * @test
 */
    public function set_existingOffsetAndValidValue()
    {
        $this->subject->set('offset3', []);
        $this->assertSame([], $this->subject->get('offset3'));
    }

    /**
     * @test
     */
    public function set_existingPathAndValidValue()
    {
        $this->subject->set('path2.path2', 'test');
        $this->assertSame('test', $this->subject->get('path2.path2'));
        $this->assertSame('test', $this->subject['path2']['path2']);

        $this->subject->set('path3.path3.path3', 'test');
        $this->assertSame('test', $this->subject->get('path3.path3.path3'));
        $this->assertSame('test', $this->subject['path3']['path3']['path3']);
    }

    /**
     * @test
     * @expectedException \Neat\Data\Exception\UnexpectedValueException
     */
    public function set_requiredOffsetAndInvalidValue_throwsException()
    {
        $this->subject->set('offset2', null);
    }

    /**
     * @test
     * @expectedException \Neat\Data\Exception\InvalidArgumentException
     */
    public function set_existingOffsetAndInvalidValue_throwsException()
    {
        $this->subject->set('offset3', 'test');
    }

    /**
     * @test
     * @expectedException \Neat\Data\Exception\InvalidArgumentException
     */
    public function set_existingPathAndInvalidValue_throwsException()
    {
        $this->subject->set('path2.path2', 1);
    }

    /**
     * @test
     * @expectedException \Neat\Data\Exception\UnexpectedValueException
     */
    public function set_rootPath_throwsException()
    {
        $this->subject->set('.', 'test');
    }

    /**
     * @test
     * @expectedException \Neat\Data\Exception\UnexpectedValueException
     */
    public function set_emptyPath_throwsException()
    {
        $this->subject->set('', 'test');
    }

    /**
     * @test
     * @expectedException \Neat\Data\Exception\OverflowException
     */
    public function set_nonExistingOffset()
    {
        $this->subject->set('Offset1', 'test');
    }

    /**
     * @test
     */
    public function setValues_existingOffsets()
    {
        $this->subject->setValues([
            'offset2' => 'test',
            'offset3' => [],
        ]);
    }

    /**
     * @test
     * @expectedException \Neat\Data\Exception\UnexpectedValueException
     */
    public function setValues_missingRequiredOffsets_throwsException()
    {
        $this->subject->setValues([]);
    }

    /**
     * @test
     */
    public function getValues_returnsArray()
    {
        $expected = [
            'offset1' => 1,
            'offset2' => null,
            'offset3' => null,
            'path1' => null,
            'path2' => null,
            'path3' => null,
        ];

        $this->assertSame($expected, $this->subject->getValues());
    }

    /**
     * @test
     */
    public function toArray_returnsArray()
    {
        $expected = [
            'offset1' => 1,
            'offset2' => null,
            'offset3' => null,
            'path1' => [],
            'path2' => ['path2' => null],
            'path3' => ['path3' => ['path3' => 'path3_test']],
        ];

        $this->assertSame($expected, $this->subject->toArray());
    }
}