<?php
namespace Neat\Http\Message;

use Psr\Http\Message\UriInterface;

/**
 * URI value object.
 */
class Uri implements UriInterface
{
    /** @var string */
    private $scheme = 'http';

    /** @var string */
    private $user = '';

    /** @var null|string */
    private $pass;

    /** @var string */
    private $host = '';

    /** @var null|int */
    private $port;

    /** @var string */
    private $path = '';

    /** @var string */
    private $query = '';

    /** @var string */
    private $fragment = '';

    /** @var array */
    private $map = [
        'http'  => 80,
        'https' => 443,
    ];

    /**
     * Constructor.
     *
     * @param null|string $uri The URI string
     */
    public function __construct($uri = null)
    {
        if ($uri) {
            $this->parseUri($uri);
        }
    }

    /**
     * Retrieves the scheme component of the URI.
     *
     * @return string The URI scheme.
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Retrieves the authority component of the URI.
     *
     * @return string The URI authority, in "[user-info@]host[:port]" format.
     */
    public function getAuthority()
    {
        if (!$this->host) {
            return '';
        }

        $authority = $this->host;
        $userInfo  = $this->getUserInfo();
        $port      = $this->getPort();

        if ($userInfo) {
            $authority = $userInfo . '@' . $authority;

            if ($port) {
                $authority .= ':' . $port;
            }
        }

        return $authority;
    }

    /**
     * Retrieves the user information component of the URI.
     *
     * @return string The URI user information, in "username[:password]" format.
     */
    public function getUserInfo()
    {
        if (!$this->user) {
            return '';
        }

        $userInfo = $this->user;

        if ($this->pass) {
            $userInfo .= ':' . $this->pass;
        }

        return $userInfo;
    }

    /**
     * Retrieves the host component of the URI.
     *
     * @return string The URI host.
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Retrieves the port component of the URI.
     *
     *  @return null|int The URI port.
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Retrieves the path component of the URI.
     *
     * @return string The URI path.
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Retrieve the query string of the URI.
     *
     * @return string The URI query.
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Retrieves the fragment component of the URI.
     *
     * @return string The URI fragment.
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * Returns an instance with the specified scheme.
     *
     * @param string $scheme The scheme to use with the new instance.
     *
     * @return self A new instance with the specified scheme.
     */
    public function withScheme($scheme)
    {
        $new = clone $this;

        $this->assertIsString('scheme', $scheme);
        $this->scheme = $scheme;

        return $new;
    }

    /**
     * Returns an instance with the specified user information.
     *
     * @param string      $user     The user name to use for authority.
     * @param null|string $password The password associated with username.
     *
     * @return self A new instance with the specified user information.
     */
    public function withUserInfo($user, $password = null)
    {
        $new = clone $this;

        $this->assertIsString('user', $user);
        $this->user = $user;

        if ($password) {
            $this->assertIsString('password', $password);
            $this->pass = $password;
        }

        return $new;
    }

    /**
     * Returns an instance with the specified host.
     *
     * @param string $host The hostname to use with the new instance.
     *
     * @return self A new instance with the specified host.
     */
    public function withHost($host)
    {
        $new = clone $this;

        $this->assertIsString('host', $host);
        $this->host = $host;

        return $new;
    }

    /**
     * Returns an instance with the specified port.
     *
     * @param null|int $port The port to use with the new instance.
     *
     * @return self A new instance with the specified port.
     */
    public function withPort($port)
    {
        $new = clone $this;

        $this->assertIsInteger('port', $port);
        $this->port = $port;

        return $new;
    }

    /**
     * Returns an instance with the specified path.
     *
     * @param string $path The path to use with the new instance.
     *
     * @return self A new instance with the specified path.
     */
    public function withPath($path)
    {
        $new = clone $this;

        $this->assertIsString('path', $path);
        $this->path = $path;

        return $new;
    }

    /**
     * Returns an instance with the specified query string.
     *
     * @param string $query The query string to use with the new instance.
     *
     * @return self A new instance with the specified query string.
     */
    public function withQuery($query)
    {
        $new = clone $this;

        $this->assertIsString('query', $query);
        $this->query = $query;

        return $new;
    }

    /**
     * Returns an instance with the specified URI fragment.
     *
     * @param string $fragment The fragment to use with the new instance.
     *
     * @return self A new instance with the specified fragment.
     */
    public function withFragment($fragment)
    {
        $new = clone $this;

        $this->assertIsString('fragment', $fragment);
        $this->fragment = $fragment;

        return $new;
    }

    /**
     * Returns the string representation as a URI reference.
     *
     * @return string An URI string
     */
    public function __toString()
    {
        return $this->createUri();
    }

    /**
     * Parses the URI string.
     *
     * @param string $uri The URI String
     *
     * @return void
     *
     * @throws Exception\InvalidArgumentException for malformed URI string
     */
    private function parseUri($uri)
    {
        $parts = parse_url($uri);

        if (false === $parts) {
            $msg = 'Uri is malformed.';
            throw new Exception\InvalidArgumentException($msg);
        }

        foreach ($parts as $key => $value) {
            $this->$key = $value;
        }

        if (isset($this->map[$this->scheme]) &&
            $this->port == $this->map[$this->scheme]
        ) {
            $this->port = null;
        }
    }

    /**
     * Creates an URI string.
     *
     * @return string An URI string
     */
    private function createUri()
    {
        $uri       = '';
        $scheme    = $this->getScheme();
        $authority = $this->getAuthority();
        $path      = $this->getPath();
        $query     = $this->getQuery();
        $fragment  = $this->getFragment();

        if ($scheme) {
            $uri .= sprintf('%s://', $scheme);
        }

        if ($authority) {
            $uri .= $authority;
        }

        if ($path) {
            $uri .= '/' . trim($path, '/');
        }

        if ($query) {
            $uri .= sprintf('?%s', $query);
        }

        if ($fragment) {
            $uri .= sprintf('#%s', $fragment);
        }

        return $uri;
    }

    /**
     * @param string $property Name of property to check
     * @param mixed  $value    Value of property to check
     *
     * @return void
     *
     * @throws Exception\InvalidArgumentException for invalid value
     */
    private function assertIsString($property, $value)
    {
        if (!is_string($value)) {
            $msg = sprintf('Value of "%s" should be a string.', $property);
            throw new Exception\InvalidArgumentException($msg);
        }
    }

    /**
     * @param string $property Name of property to check
     * @param mixed  $value    Value of property to check
     *
     * @return void
     *
     * @throws Exception\InvalidArgumentException for invalid value
     */
    private function assertIsInteger($property, $value)
    {
        if (!is_int($value)) {
            $msg = sprintf('Value of "%s" should be a integer.', $property);
            throw new Exception\InvalidArgumentException($msg);
        }
    }
}
