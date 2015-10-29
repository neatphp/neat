<?php
namespace Neat\Test\Container\Fixture;

class Service3
{
    private $property1;
    private $property2;

    public function setProperty1(Service4 $param)
    {
        $this->property1 = $param;
    }

    public function setProperty2(Service5 $param)
    {
        $this->property2 = $param;
    }

    public function getProperty1()
    {
        return $this->property1;
    }

    public function getProperty2()
    {
        return $this->property2;
    }
}
