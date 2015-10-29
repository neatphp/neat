<?php
namespace Neat\Router;

use Neat\Base\Object;

/**
 * Route setting.
 *
 * @property string pattern
 * @property array  defaultValues
 * @property array  requiredParams
 * @property array  httpMethods
 */
class RouteSetting extends Object
{
    /**
     * Constructor.
     *
     * @param array $setting
     */
    public function __construct(array $setting)
    {
        $this->getProperties()->requireOffsets(['pattern'])->setValues($setting);
    }
}