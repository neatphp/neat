<?php
namespace Neat\Test\Container\Fixture;

class Service6
{
    private $property;

    public function setProperty(Service4 $param)
    {
        $this->property = $param;
    }

    public function getProperty()
    {
        return $this->property;
    }
}
