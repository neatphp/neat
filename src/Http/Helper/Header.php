<?php
namespace Neat\Http\Helper;

use Neat\Base\Object;

/**
 * Header.
 *
 * @property string cache_control
 * @property string date
 * @property string pragma
 * @property string accept
 * @property string accept_charset
 * @property string accept_encoding
 * @property string accept_language
 * @property string accept_ranges
 * @property string authorization
 * @property string connection
 * @property string cookie
 * @property string host
 * @property string if_modified_since
 * @property string if_none_match
 * @property string user_agent
 * @property string age
 * @property string allow
 * @property string content_encoding
 * @property string content_language
 * @property string content_length
 * @property string content_location
 * @property array  content_disposition
 * @property string content_md5
 * @property string content_range
 * @property array  content_type
 * @property string etag
 * @property string expires
 * @property string last_modified
 * @property string location
 * @property array  refresh
 * @property string server
 * @property string set_cookie
 */
class Header extends Object
{
    /** @var array */
    private $formats = [
    	'cache_control' => 'Cache-Control: %s',
    	'date' => 'Date: %s GMT',
    	'pragma' => 'Pragma: %s',
    	'accept' => 'Accept: %s',
    	'accept_charset' => 'Accept-Charset: %s',
    	'accept_encoding' => 'Accept-Encoding: %s',
    	'accept_language' => 'Accept-Language: %s',
    	'accept_ranges' => 'Accept-Ranges: %s',
    	'authorization' => 'Authorization: Basic %s',
    	'connection' => 'Connection: %s',
    	'cookie' => 'Cookie: %s',
    	'host' => 'Host: %s',
    	'if_modified_since' => 'If-Modified-Since: %s GMT',
    	'if_none_match' => 'If-None-Match: %s',
    	'user_agent' => 'User-Agent: %s',
    	'age' => 'Age: %s',
    	'allow' => 'Allow: %s',
    	'content_encoding' => 'Content-Encoding: %s',
    	'content_language' => 'Content-Language: %s',
    	'content_length' => 'Content-Length: %s',
    	'content_location' => 'Content-Location: %s',
    	'content_disposition' => 'Content-Disposition: %s; filename=%s',
    	'content_md5' => 'Content-MD5: %s',
    	'content_range' => 'Content-Range: %s',
    	'content_type' => 'Content-Type: %s; charset=%s',
    	'etag' => 'ETag: %s',
    	'expires' => 'Expires: %s GMT',
    	'last_modified' => 'Last-Modified: %s GMT',
    	'location' => 'Location: %s',
    	'refresh' => 'Refresh: %s; url=%s',
    	'server' => 'Server: %s',
    	'set_cookie' => 'Set-Cookie: %s',
    ];

    /**
     * Sends header.
     *
     * @return void
     */
    public function send()
    {
        foreach ($this->toArray() as $value) {
            if (isset($value)) header($value);
        }
    }

    /**
     * Returns header as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $values = [];
        foreach ($this->getProperties() as $name => $value) {
            $format = $this->formats[$name];

            if (is_string($value)) {
                $values[$name] = sprintf($format, $value);

                continue;
            }

            if (is_array($value)) {
                array_unshift($value, $format);
                $values[$name] = call_user_func_array('sprintf', $value);

                continue;
            }
        }

        return $values;
    }
}