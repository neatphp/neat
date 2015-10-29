<?php
namespace Neat\Test\Container\Fixture;

class Service5
{
    private $property;

    public function setProperty(Service6 $param)
    {
        $this->property = $param;
    }

    public function getProperty()
    {
        return $this->property;
    }
}
