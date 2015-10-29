<?php
namespace Neat\Test\Data\Fixture;


class Filter extends \Neat\Data\Helper\Filter
{
    public function __construct()
    {
        parent::__construct([
            'offset2' => function ($value) {
                if (is_string($value)) return $value . ' is filtrated';

                return $value;
            },
        ]);
    }

    public function filtrateOffset1($value)
    {
        return $value;
    }
}