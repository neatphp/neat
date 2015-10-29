<?php
namespace Neat\Test\Util;

use Neat\Util\Color;

class ColorTest extends \PHPUnit_Framework_TestCase
{
    /** @var Color */
    private $subject;

    protected function setUp()
    {
        $this->subject = new Color;
    }

    /**
     * @test
     */
    public function hexToRgb_returnsArray()
    {
        $this->assertSame([255, 255, 255], $this->subject->hexToRgb('#FFF'));
        $this->assertSame([255, 255, 255], $this->subject->hexToRgb('#FFFFFF'));
    }

    /**
     * @test
     */
    public function rgbToHex_returnsString()
    {
        $this->assertSame('FFFFFF', $this->subject->rgbToHex([255, 255, 255]));
        $this->assertSame('000000', $this->subject->rgbToHex([0, 0, 0]));
    }

    /**
     * @test
     */
    public function darken_returnsString()
    {
        $this->assertSame('808080', $this->subject->darken('#FFF', 0.5));
        $this->assertSame('000000', $this->subject->darken('#000', 0.5));
    }

    /**
     * @test
     */
    public function brighten_returnsString()
    {
        $this->assertSame('808080', $this->subject->brighten('#000', 0.5));
        $this->assertSame('FFFFFF', $this->subject->brighten('#FFF', 0.5));
    }

    /**
     * @test
     */
    public function gradient_returnsArray()
    {
        $gradient = [
            'darkest' => '404040',
            'darker' => '808080',
            'dark' => 'bfbfbf',
            'base' => '#FFF',
            'bright' => 'FFFFFF',
            'brighter' => 'FFFFFF',
            'brightest' => 'FFFFFF',
        ];

        $this->assertSame($gradient, $this->subject->gradient('#FFF'));
    }
}
