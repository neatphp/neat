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
        $this->getProperties()
            ->requireOffsets(['name'])
            ->getFilter()->setRule('expire', function (Datetime $value) {
                if (isset($value)) return $value->getTimestamp();
                return $value;
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
     * Returns cookie as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getProperties()->toArray();
    }
}