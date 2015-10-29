<?php
namespace Neat\Util;

/**
 * Color utility.
 */
Class Color
{
    /**
     * Converts hexadecimal color to rgb color.
     *
     * @param string $hex
     *
     * @return array
     */
    public function hexToRgb($hex)
    {
        if (substr($hex, 0, 1) == '#') $hex = substr($hex, 1);

        if (3 == strlen($hex)) {
            $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        return array($r, $g, $b);
    }

    /**
     * Converts rgb color to hexadecimal color.
     *
     * @param array $rgb
     *
     * @return string
     */
    public function rgbToHex(array $rgb)
    {
        $hex = '';
        foreach ($rgb as $value) {
            $vector = dechex($value);
            if ($value <= 16) $vector = '0' . $vector;
            if ($value >= 255) $vector = 'FF';
            $hex .= $vector;
        }

        return $hex;
    }

    /**
     * Darkens a color.
     *
     * @param string|array $color
     * @param double       $opacity range is between 0 and 1
     *
     * @return string|array
     */
    public function darken($color, $opacity)
    {
        $rgb = is_string($color) ? $this->hexToRgb($color) : $color;
        foreach ($rgb as & $value) $value = round($value * $opacity);
        $color = is_string($color) ? $this->rgbToHex($rgb) : $rgb;

        return $color;
    }

    /**
     * Brightens a color.
     *
     * @param string|array $color
     * @param double $opacity range is between 0 and 1
     *
     * @return string|array
     */
    public function brighten($color, $opacity)
    {
        $rgb = is_string($color) ? $this->hexToRgb($color) : $color;
        $offset = round(255 * (1.0 - $opacity));
        foreach ($rgb as & $value) $value = round($value * $opacity) + $offset;
        $color = is_string($color) ? $this->rgbToHex($rgb) : $rgb;

        return $color;
    }

    /**
     * Returns a color gradient.
     *
     * @param string|array $color
     *
     * @return array
     */
    public function gradient($color)
    {
        $opacities = array(
            'darkest' => -0.25,
            'darker' => -0.50,
            'dark' => -0.75,
            'base' => 1.0,
            'bright' => 0.50,
            'brighter' => 0.25,
            'brightest' => 0.10,
        );

        $gradient = array();
        foreach ($opacities as $name => $opacity) {
            switch (true) {
                case 1 == $opacity:
                    $gradient[$name] = $color;
                    break;

                case $opacity < 0:
                    $gradient[$name] = $this->darken($color, abs($opacity));
                    break;

                case $opacity > 0:
                    $gradient[$name] = $this->brighten($color, $opacity);
            }
        }

        return $gradient;
    }
}