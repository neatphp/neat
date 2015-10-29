<?php
namespace Neat\Test\Container\Fixture;

class Service2
{
    private $property1;
    private $property2;

    public function __construct(Service3 $param1 = null, Service4 $param2 = null)
    {
        $this->property1 = $param1;
        $this->property2 = $param2;
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