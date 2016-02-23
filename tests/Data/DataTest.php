<?php
namespace Neat\Test\Data;

use Neat\Data\Data;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /** @var Data */
    private $subject;

    protected function setUp()
    {
        $this->subject = new Data(['offset1', 'offset2', 'offset3'], true);
        $this->subject->load([
            'offset1' => '',
            'offset3' => function () {
                return new Data(['offset1', 'offset2', 'offset3']);
            },
        ]);

        $this->subject->getFilter()->append('offset1', function ($value) {
            $value .= 'filtrated';

            return $value;
        });

        $this->subject->getValidator()
            ->append('offset2', 'string')
            ->append('offset3', 'Neat\Data\Data');
    }

    public function testClone_returnsData()
    {
        $data = $this->subject['offset3'];
        $filter = $this->subject->getFilter();
        $validator = $this->subject->getValidator();
        $clone = clone $this->subject;

        $this->assertNotSame($filter, $clone->getFilter());
        $this->assertNotSame($validator, $clone->getValidator());
        $this->assertNotSame($data, $clone['offset3']);
    }

    public function testOffsetExists_withExistingOffset_returnsTrue()
    {
        $this->assertTrue(isset($this->subject['offset1']));
        $this->assertTrue(isset($this->subject['offset3']));
    }

    public function testOffsetExists_withExistingOffsetAndNull_returnsFalse()
    {
        $this->assertFalse(isset($this->subject['offset2']));
    }

    public function testOffsetExists_withNonExistingOffset_returnsFalse()
    {
        $this->assertFalse(isset($this->subject['offset']));
    }

    public function testOffsetGet_withExistingOffsetAndFilter_returnsString()
    {
        $this->assertSame('filtrated', $this->subject['offset1']);
    }

    public function testOffsetGet_withExistingOffsetAndLazyLoad_returnsData()
    {
        $this->assertInstanceOf('Neat\Data\Data', $this->subject['offset3']);
    }

    public function testOffsetGet_withNonExistingOffset_throwsException()
    {
        $this->setExpectedException('Neat\Data\Exception\OutOfBoundsException');
        $this->subject['offset'];
    }

    public function testOffsetSet_withExistingOffsetAndInvalidValue_throwsException()
    {
        $this->setExpectedException('Neat\Data\Exception\InvalidArgumentException');
        $this->subject['offset2'] = [];
    }

    public function testOffsetSet_withExistingOffsetAndReadonly_throwsException()
    {
        $this->setExpectedException('Neat\Data\Exception\ReadonlyException');
        $this->subject['offset1'] = '';
    }

    public function testOffsetSet_withNonExistingOffset_throwsException()
    {
        $this->setExpectedException('Neat\Data\Exception\OutOfBoundsException');
        $this->subject['offset'] = '';
    }

    public function testOffsetUnset_withExistingOffset()
    {
        unset($this->subject['offset1']);
        $this->assertFalse(isset($this->subject['offset1']));
    }

    public function testOffsetUnset_withNonExistingOffset()
    {
        unset($this->subject['offset']);
        $this->assertFalse(isset($this->subject['offset']));
    }

    public function testToArray_returnsArray()
    {
        $expected = [
            'offset1' => 'filtrated',
            'offset2' => null,
            'offset3' => [
                'offset1' => '',
                'offset2' => null,
                'offset3' => null,
            ],
        ];

        $this->subject['offset3']['offset1'] = '';
        $this->assertSame($expected, $this->subject->toArray());
    }
}