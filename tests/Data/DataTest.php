<?php
namespace Neat\Test\Data;

use Neat\Data\Data;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /** @var Data */
    private $subject;

    protected function setUp()
    {
        $this->subject = new Data;
        $lazyload = function () {
            $data = new Data;
            $data
                ->init('offset1')
                ->init('offset2')
                ->init('offset3');

            return $data;
        };

        $this->subject
            ->init('offset1', null, true)
            ->init('offset2', null, false)
            ->init('offset3', $lazyload, true)
            ->init('offset4', $lazyload, false);

        $this->subject->getFilter()
            ->append('offset1', function ($value) {
                $value .= 'filtrated';

                return $value;
            });

        $this->subject->getValidator()
            ->append('offset1', 'string')
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

    public function testOffsetExists_withNonExistingOffset_returnsFalse()
    {
        $this->assertFalse(isset($this->subject['offset']));
    }

    public function testOffsetGet_withExistingOffsetAndFilter_returnsString()
    {
        $this->subject['offset1'] = '';
        $this->assertSame('filtrated', $this->subject['offset1']);
    }

    public function testOffsetGet_withExistingOffsetAndLazyLoad_returnsData()
    {
        $this->assertInstanceOf('Neat\Data\Data', $this->subject['offset3']);
    }

    public function testOffsetGet_witLazyLoadAndReadonly_returnsSameData()
    {
        $this->assertSame($this->subject['offset3'], $this->subject['offset3']);
    }

    public function testOffsetGet_witLazyLoadAndReadonly_returnsDifferentData()
    {
        $this->assertNotSame($this->subject['offset4'], $this->subject['offset4']);
    }

    public function testOffsetGet_withNonExistingOffset_throwsException()
    {
        $this->setExpectedException('Neat\Data\Exception\OutOfBoundsException');
        $this->subject['offset'];
    }

    public function testOffsetSet_withExistingOffsetAndInvalidValue_throwsException()
    {
        $this->setExpectedException('Neat\Data\Exception\InvalidArgumentException');
        $this->subject['offset1'] = [];
    }

    public function testOffsetSet_withExistingOffsetAndReadonly_throwsException()
    {
        $this->subject['offset1'] = '';
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
        $this->subject['offset1'] = '';
        $this->subject['offset3']['offset1'] = 1;
        $this->subject['offset3']['offset2'] = '';
        $this->subject['offset3']['offset3'] = 'test';

        $expected = [
            'offset1' => 'filtrated',
            'offset2' => null,
            'offset3' => [
                'offset1' => 1,
                'offset2' => '',
                'offset3' => 'test',
            ],
            'offset4' => [
                'offset1' => null,
                'offset2' => null,
                'offset3' => null,
            ],
        ];

        $this->assertSame($expected, $this->subject->toArray());
    }
}