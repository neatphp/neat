<?php
namespace Neat\Router;

use Neat\Data\Data;
use Neat\Data\Exception\ExceptionInterface as DataException;

/**
 * Route tries to match the requested URL against a series of URL patterns.
 */
class Route
{
    /** @var string */
    private $pattern;

    /** @var Data */
    private $urlParams;

    /** @var Data */
    private $httpMethods;

    /** @var array */
    private $segments = [];

    /** @var array */
    private $wildcards = [];

    /** @var array */
    private $optionalParams = [];

    /** @var string */
    private $error;

    /**
     * Constructor.
     *
     * @param string $pattern Pattern is in the form of 'xxx.:param.xxx/xxx/:param+'.
     *                           Segment 'xxx' means a static text at the position.
     *                           Segment ':param' means a parameter at the position.
     *                           Segment ':param+' means a wildcard parameter at the position.
     */
    public function __construct($pattern)
    {
        $this->pattern = $pattern;
        preg_match_all('/:([\w]+)\+?/', $pattern, $matches);
        foreach ($matches[0] as $key => $segment) {
            $this->segments[$matches[1][$key]] = $segment;
        }

        $this->urlParams = new Data;
        $this->urlParams->loadOffsets($matches[1]);

        preg_match_all('/\(\/:([\w]+)\+?/', $pattern, $matches);
        foreach ($matches[1] as $param) {
            $this->optionalParams[] = $param;
        }
        
        $this->httpMethods = new Data;
        $this->httpMethods->setValues([
            'POST'   => false,
            'GET'    => false,
            'PUT'    => false,
            'DELETE' => false,
        ]);
    }

    /**
     * Returns the pattern.
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Returns parameters.
     *
     * @return Data
     */
    public function getUrlParams()
    {
        return $this->urlParams;
    }

    /**
     * Returns supported methods.
     *
     * @return Data
     */
    public function getHttpMethods()
    {
        return $this->httpMethods;
    }

    /**
     * Returns error.
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Matches a URI.
     *
     * @param string $uri
     *
     * @return bool
     */
    public function match($uri)
    {
        $regexPattern = $this->parse();
        if (!preg_match($regexPattern, $uri, $matches)) {
            $this->error = sprintf('Pattern "%s" does not match uri "%s".', $this->pattern, $uri);

            return false;
        }

        $values = [];
        foreach ($matches as $key => $value) {
            if (is_int($key)) continue;

            $value = urldecode($value);
            if (isset($this->wildcards[$key])) $value = explode('/', $value);
            $values[$key] = $value;
        }

        try {
            $this->urlParams->setValues($values);
            $this->error = null;
        } catch (DataException $e) {
            $this->error = $e->getMessage();

            return false;
        }

        return true;
    }

    /**
     * Assembles a URI.
     *
     * @param array $values
     *
     * @return string
     */
    public function assemble(array $values)
    {
        $params = clone $this->urlParams;
        $params->reset()->setValues($values);

        $search = [];
        foreach ($params as $param => $value) {
            $this->assertParamValueIsNotEmpty($param, $value);
            $search[] = '#:' . $param . '\+?(?!\w)#';
        }

        $uri = preg_replace($search, $params->toArray(), $this->pattern);
        $uri = preg_replace('#\(/?:.+\)|\(|\)#', '', $uri);
        $uri = rtrim($uri, '/');

        return $uri;
    }

    /**
     * Parses pattern to regular expression pattern.
     *
     * @return string
     */
    private function parse()
    {
        $replace = [];
        foreach ($this->segments as $param => $segment) {
            if ('+' == substr($segment, -1)) {
                $this->wildcards[$param] = true;
                $replace[] = sprintf('(?P<%s>.+)', $param);
            } else {
                $replace[] = sprintf('(?P<%s>[^/]+)', $param);
            }
        }

        $pattern = str_replace(')', ')?', rtrim($this->pattern, '/'));
        $regexPattern = str_replace($this->segments, $replace, $pattern);
        $regexPattern = sprintf('#^%s$#', $regexPattern);

        return $regexPattern;
    }

    /**
     * @param string $param
     * @param mixed $value
     * @throws Exception\UnexpectedValueException
     */
    private function assertParamValueIsNotEmpty($param, $value)
    {
        if (!in_array($param, $this->optionalParams) && empty($value)) {
            $msg = sprintf('Parameter "%s" is not optional and should not be empty.', $param);
            throw new Exception\UnexpectedValueException($msg);
        }
    }
}