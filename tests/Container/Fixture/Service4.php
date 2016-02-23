<?php
namespace Neat\Test\Container\Fixture;

class Service4
{
    private $property;

    public function setProperty(Service5 $param)
    {
        $this->property = $param;
    }

    public function getProperty()
    {
        return $this->property;
    }
}
