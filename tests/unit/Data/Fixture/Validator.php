<?php
namespace Neat\Test\Data\Fixture;

class Validator extends \Neat\Data\Helper\Validator
{
    public function __construct()
    {
        parent::__construct([
            'offset1' => 'int',
            'offset2' => 'int',
            'offset3' => 'array',
            'path2' => 'string',
        ]);
    }

    public function validateOffset2($value)
    {
        return is_string($value);
    }
}