<?php
namespace Neat\Http\Helper;

use DateTime;
use Neat\Base\Object;

/**
 * Http cookie.
 *
 * @property string   $name
 * @property string   $value
 * @property Datetime $expire
 * @property string   $path
 * @property string   $domain
 * @property bool     $secure
 * @property bool     $httponly
 */
class Cookie extends Object
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $properties = $this->getProperties();

        $properties
            ->getValidator()
            ->append('name', 'required');

        $properties
            ->getFilter()
            ->append('expire', function (Datetime $value) {
                return $value->getTimestamp();
            });
    }

    /**
     * Sends the cookie.
     *
     * @param bool|false $raw If false, sends the cookie without url-encoding the value.
     *
     * @return bool If output exists prior to calling of this method, returns false.
     *              This does not indicate whether the user accepted the cookie.
     */
    public function send($raw = false)
    {
        $function = $raw ? 'setrawcookie' : 'setcookie';

        return call_user_func_array($function, $this->toArray());
    }

    /**
     * Returns values as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getProperties()->toArray();
    }
}