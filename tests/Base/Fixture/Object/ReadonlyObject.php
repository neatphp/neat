<?php
namespace Neat\Test\Base\Fixture\Object;

/**
 * @property $property3
 */
class ReadonlyObject extends DefaultObject
{
    protected $readonly = true;
}