<?php
namespace Neat\Http;

use Neat\Data\Data;
use Neat\Base\Object;

/**
 * Http request.
 *
 * @property \Neat\Data\Data pathParams
 * @property \Neat\Data\Data getParams
 * @property \Neat\Data\Data postParams
 * @property \Neat\Data\Data cookieParams
 * @property \Neat\Data\Data filesParams
 * @property \Neat\Data\Data serverParams
 */
class Request extends Object
{
    const METHOD_HEAD     = 'HEAD';
    const METHOD_GET      = 'GET';
    const METHOD_POST     = 'POST';
    const METHOD_PUT      = 'PUT';
    const METHOD_PATCH    = 'PATCH';
    const METHOD_DELETE   = 'DELETE';
    const METHOD_PURGE    = 'PURGE';
    const METHOD_OPTIONS  = 'OPTIONS';
    const METHOD_TRACE    = 'TRACE';
    const METHOD_CONNECT  = 'CONNECT';
    const METHOD_OVERRIDE = '_METHOD';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->pathParams = function () {
            $data = new Data;

            return $data;
        };

        $this->getParams = function () {
            $data = new Data(true, true);
            $data->loadValues($_GET);

            return $data;
        };

        $this->postParams = function () {
            $data = new Data(true, true);
            $data->loadValues($_POST);

            return $data;
        };

        $this->cookieParams = function () {
            $data = new Data(true, true);
            $data->loadValues($_COOKIE);

            return $data;
        };

        $this->filesParams = function () {
            $data = new Data(true, true);
            $data->loadValues($_FILES);

            return $data;
        };

        $this->serverParams = function () {
            $data = new Data(true, true);
            $data->loadValues($_SERVER);

            return $data;
        };
    }

    /**
     * Returns value of a parameter.
     *
     * Order of precedence: GET, PATH, POST
     *
     * @param string $name
     * @param string $default
     *
     * @return mixed
     */
    public function get($name, $default = null)
    {
        if (isset($this->getParams[$name])) return $this->getParams[$name];
        if (isset($this->pathParams[$name])) return $this->pathParams[$name];
        if (isset($this->postParams[$name])) return $this->postParams[$name];

        return $default;
    }

    /**
     * Returns the scheme.
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->serverParams->has('HTTPS') ? 'https' : 'http';
    }

    /**
     * Returns the port.
     *
     * @return string
     */
    public function getPort()
    {
        $port = $this->search($this->serverParams, 'SERVER_PORT', 'HTTP_HOST');

        if ($port) {
            $pos = strrpos($port, ':');
            if (false !== $pos) $port = substr($port, $pos + 1);
        } else {
            $port = 'https' == $this->getScheme() ? '443' : '80';
        }

        return $port;
    }

    /**
     * Returns the host.
     *
     * @return string
     */
    public function getHost()
    {
        $host = $this->search($this->serverParams, 'HTTP_HOST', 'SERVER_NAME', 'SERVER_ADDR');
        if ($host) $host = strtolower(preg_replace('/:\d+$/', '', trim($host)));

        return $host;
    }

    /**
     * Returns the http host.
     *
     * @return string
     */
    public function getHttpHost()
    {
        $scheme = $this->getScheme();
        $port   = $this->getPort();
        $host   = $this->getHost();

        if (('http' != $scheme || '80' != $port) && ('https' != $scheme || '443' != $port)) {
            $host .= ':'.$port;
        }

        return $host;
    }

    /**
     * Returns the path info.
     *
     * @return string
     */
    public function getPathInfo()
    {
        return $this->serverParams['PATH_INFO'];
    }

    /**
     * Returns the http method.
     *
     * @return string
     */
    public function getMethod()
    {
        return isset($this->serverParams['REQUEST_METHOD']) ? $this->serverParams['REQUEST_METHOD'] : self::METHOD_GET;
    }

    /**
     * Tells whether the request is a XMLHttpRequest.
     *
     * @return bool
     */
    public function isXmlHttpRequest()
    {
        return isset($this->serverParams['X_REQUESTED_WITH']) && 'XMLHttpRequest' == $this->serverParams['X_REQUESTED_WITH'];
    }

    /**
     * Searches data for not empty value.
     *
     * @param Data $data
     *
     * @return string
     */
    private function search(Data $data)
    {
        $args = func_get_args();
        array_shift($args);

        foreach ($args as $name) {
            if ($data->has($name)) {
                $value = $data->get($name);
                if (!empty($value)) return $value;
            }
        }

        return '';
    }
}