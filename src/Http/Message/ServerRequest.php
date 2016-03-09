<?php
namespace Neat\Http\Message;

use Neat\Data\Data;
use Neat\Http\Message\Message;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Http request.
 *
 * @property \Neat\Http\Message\UriInterface $uri
 * @property string                          $method
 * @property string                          $target
 * @property \Neat\Data\Data                 pathParams
 * @property \Neat\Data\Data                 getParams
 * @property \Neat\Data\Data                 postParams
 * @property \Neat\Data\Data                 cookieParams
 * @property \Neat\Data\Data                 filesParams
 * @property \Neat\Data\Data                 serverParams
 */
class Request extends Message implements RequestInterface
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
     *
     * @param UriInterface    $uri     Uri
     * @param string          $method  Request method
     * @param StreamInterface $body    Body
     * @param array           $headers Headers
     */
    public function __construct(
        UriInterface $uri,
        $method = self::METHOD_GET,
        StreamInterface $body = null,
        array $headers = array()
    ) {
        $this->uri = $uri;
        $this->method = $method;
        $this->body = $body;
        $this->headers = $headers;

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
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     *
     * If no URI is available, and no request-target has been specifically
     * provided, this method MUST return the string "/".
     *
     * @return string
     */
    public function getRequestTarget()
    {
        if ($this->requestTarget) {
            return $this->requestTarget;
        }

        $target = '/';

        if ($this->uri) {
            $path = $this->uri->getPath();
            $query = $this->uri->getQuery();

            if ($path) {
                $target = $path;
                if ($query) {
                    $target .= '?' . $query;
                }
            }
        }

        return $target;
    }

    /**
     * Return an instance with the specific request-target.
     *
     * If the request needs a non-origin-form request-target — e.g., for
     * specifying an absolute-form, authority-form, or asterisk-form —
     * this method may be used to create an instance with the specified
     * request-target, verbatim.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request target.
     *
     * @link http://tools.ietf.org/html/rfc7230#section-2.7 (for the various
     *     request-target forms allowed in request messages)
     * @param mixed $requestTarget
     * @return self
     */
    public function withRequestTarget($requestTarget)
    {
        // TODO: Implement withRequestTarget() method.
    }

    /**
     * Return an instance with the provided HTTP method.
     *
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request method.
     *
     * @param string $method Case-sensitive method.
     * @return self
     * @throws \InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod($method)
    {
        // TODO: Implement withMethod() method.
    }

    /**
     * Retrieves the URI instance.
     *
     * This method MUST return a UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @return UriInterface Returns a UriInterface instance
     *     representing the URI of the request.
     */
    public function getUri()
    {
        // TODO: Implement getUri() method.
    }

    /**
     * Returns an instance with the provided URI.
     *
     * This method MUST update the Host header of the returned request by
     * default if the URI contains a host component. If the URI does not
     * contain a host component, any pre-existing Host header MUST be carried
     * over to the returned request.
     *
     * You can opt-in to preserving the original state of the Host header by
     * setting `$preserveHost` to `true`. When `$preserveHost` is set to
     * `true`, this method interacts with the Host header in the following ways:
     *
     * - If the the Host header is missing or empty, and the new URI contains
     *   a host component, this method MUST update the Host header in the returned
     *   request.
     * - If the Host header is missing or empty, and the new URI does not contain a
     *   host component, this method MUST NOT update the Host header in the returned
     *   request.
     * - If a Host header is present and non-empty, this method MUST NOT update
     *   the Host header in the returned request.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @param UriInterface $uri New request URI to use.
     * @param bool $preserveHost Preserve the original state of the Host header.
     * @return self
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        // TODO: Implement withUri() method.
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
        $this->pathParams[$name] = $default;

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
        return $this->search($this->serverParams, 'PATH_INFO', 'ORIG_PATH_INFO');
    }

    /**
     * Returns the base URL.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        $path = $this->serverParams['SCRIPT_FILENAME'];
        $filename = basename($path);

        if (basename($this->serverParams['SCRIPT_NAME']) === $filename) {
            return $this->serverParams['SCRIPT_NAME'];
        } elseif (basename($this->serverParams['PHP_SELF']) === $filename) {
            return $this->serverParams['PHP_SELF'];
        } elseif (basename($this->serverParams['ORIG_SCRIPT_NAME']) === $filename) {
            return $this->serverParams['ORIG_SCRIPT_NAME'];
        }
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