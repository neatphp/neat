<?php
namespace Neat\Test\Container\Fixture;

use Neat\Base\Component;

class Service4 extends Component
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
