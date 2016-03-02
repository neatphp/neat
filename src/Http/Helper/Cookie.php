<?php
namespace Neat\Http\Helper;

use DateTime;
use Neat\Base\Object;

/**
 * Cookie.
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
        $properties->getValidator()->append('name', '!empty');
        $properties->getFilter()->append('expire', function (Datetime $value) {
            return $value->getTimestamp();
        });
    }

    /**
     * Sends this cookie.
     *
     * @param bool|false $raw
     *
     * @return bool
     */
    public function send($raw = false)
    {
        $function = $raw ? 'setrawcookie' : 'setcookie';

        return call_user_func_array($function, $this->toArray());
    }

    /**
     * Retrieves cookie as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return ;
    }
}