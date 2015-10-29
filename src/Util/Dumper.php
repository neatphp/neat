<?php
namespace Neat\Util;

use ReflectionClass;
use ReflectionObject;
use SplObjectStorage;

/**
 * Dumper utility.
 */
class Dumper
{
    /** @var int */
    private $size;

    /** @var int */
    private $length;


    /** @var SplObjectStorage */
    private $objects;

    /** @var string */
    private $mark = '__neat_dumper_mark__';

    /**
     * Constructor.
     *
     * @param int $size
     * @param int $length
     */
    public function __construct($size = 10, $length = 50)
    {
        $this->size = $size;
        $this->length = $length;
        $this->objects = new SplObjectStorage;
    }

    /**
     * Describes information about a variable.
     *
     * @param mixed $var
     *
     * @return string
     */
    public function describe($var)
    {
        switch (true) {
            case is_array($var):
                return sprintf('array(%s)', count($var));

            case is_object($var):
                return sprintf('object(%s)', get_class($var));

            case is_bool($var):
                return sprintf('bool(%s)', $var ? 'true' : 'false');

            case is_null($var):
                return 'null';

            case is_resource($var):
                return sprintf('resource(%s)', get_resource_type($var));

            case is_string($var):
                $varLength = strlen($var);
                if ($varLength > $this->length) {
                    return sprintf('string(%s) "%s..."', $varLength, substr($var, 0, $this->length));
                } else {
                    return sprintf('string(%s) "%s"', $varLength, $var);
                }

            default:
                return sprintf('%s(%s)', gettype($var), $var);
        }
    }

    /**
     * Dumps information about a variable.
     *
     * @param mixed     $var
     * @param bool|true $recursive
     * @param int       $depth
     *
     * @return string
     */
    public function dump(& $var, $recursive = true, $depth = 0)
    {
        if (0 === $depth) $this->objects->removeAll($this->objects);

        if (is_array($var)) return $this->dumpArray($var, $recursive, $depth);

        if (is_object($var)) return $this->dumpObject($var, $recursive, $depth);

        return $this->describe($var);
    }

    /**
     * Dumps information of a backtrace.
     *
     * @param array $backtrace
     *
     * @return string
     */
    public function dumpBacktrace(array $backtrace)
    {
        $index = 0;
        $output = '';
        while ($backtrace) {
            $item = array_pop($backtrace);
            $file = isset($item['file']) ? $item['file'] : '';
            $line = isset($item['line']) ? $item['line'] : '';

            if (strpos($file, '(')) {
                $start = strpos($file, '(');
                $end = strpos($file, ')');
                $line = trim(substr($file, $start + 1, $end - $start - 1));
                $file = trim(substr($file, 0, $start));
            }

            $function = '';
            if (isset($item['function'])) {
                $function = isset($item['class']) ? $item['class'] . $item['type'] . $item['function'] : $item['function'];
            }

            $args = array();
            if (isset($item['args']) && is_array($item['args'])) {
                foreach ($item['args'] as $arg) $args[] = gettype($arg);
            }
            $args = implode(', ', $args);

            if ($file) {
                $output .= sprintf('#%s %s(%s) at %s(%s)%s', $index, $function, $args, $file, $line, PHP_EOL);
            } else {
                $output .= sprintf('#%s %s(%s)%s', $index, $function, $args, PHP_EOL);
            }

            $index++;
        }

        return $output;
    }

    /**
     * Formats information about a backtrace as HTML.
     *
     * @param array $backtrace
     *
     * @return string
     */
    public function formatBacktrace($backtrace)
    {
        $head = '<thead><tr><th>#</th><th>File</th><th>Function</th><th>Arguments</th></tr></thead>' . PHP_EOL;
        $row = '<tr class="%s" valign="top"><td>#%s</td><td>%s</td><td>%s(%s)</td><td><pre>%s</pre></td></tr>' . PHP_EOL;

        $index = 0;
        $body = '<tbody>';

        while ($backtrace) {
            $item = array_pop($backtrace);
            $file = isset($item['file']) ? $item['file'] : '';
            $line = isset($item['line']) ? $item['line'] : '';
            if (strpos($file, '(')) {
                $start = strpos($file, '(');
                $end = strpos($file, ')');
                $line = trim(substr($file, $start + 1, $end - $start - 1));
                $file = trim(substr($file, 0, $start));
            }

            $functionColumn = 'n.a.';
            $paramNames = [];
            if (isset($item['function'])) {
                if (isset($item['class'])) {
                    $functionColumn = $item['class'] . $item['type'] . $item['function'];
                    $class = new ReflectionClass($item['class']);
                    if ($class->hasMethod($item['function'])) {
                        $method = $class->getMethod($item['function']);
                        foreach ($method->getParameters() as $param) {
                            $paramNames[] = '$' . $param->getName();
                        }
                    }
                } else {
                    $functionColumn = $item['function'];
                }

            }

            $args = array();
            if (isset($item['args']) && is_array($item['args'])) {
                if (empty($item['args'])) {
                    $args[] = 'n.a.';
                } else {
                    foreach ($item['args'] as $key => $arg) {
                        $args[] = htmlspecialchars($this->dump($arg, false));
                    }
                }
            }

            $paramsColumn = implode(', ', $paramNames);
            $argsColumn = implode(PHP_EOL, $args);
            $class = $index % 2 ? 'odd' : 'even';

            if ($file) {
                $fileColumn = sprintf('%s(%s)', $file, $line);
                $body .= sprintf($row, $class, $index, $fileColumn, $functionColumn, $paramsColumn, $argsColumn);
            } else {
                $body .= sprintf($row, $class, $index, 'n.a', $functionColumn, $paramsColumn, $argsColumn);
            }

            $index++;
        }

        $body .= '</tbody>';

        return sprintf('<table class="neat-table">%s%s</table>', $head, $body);
    }

    /**
     * Formats debug records.
     *
     * @param array $records
     *
     * @return string
     */
    public function formatDebugRecords(array $records)
    {
        if (empty($records)) return 'No debug records.';

        $head = '<thead><tr><th>Variable</th><th>File</th><th>Description</th></thead>';

        $body = '<tbody>';
        $row = '<tr class="%s" valign="top"><td><pre>%s</pre></td><td>%s(%s)</td><td>%s</td></tr>';

        foreach ($records as $index => $record) {
            $variable = htmlspecialchars($this->dump($record['variable']));
            $description = isset($item['description']) ? $record['description'] : 'n.a.';
            $file = isset($record['file']) ? $record['file'] : 'n.a.';
            $line = isset($record['line']) ? $record['line'] : 'n.a.';

            $css = $index % 2 ? 'odd' : 'even';
            $body .= sprintf($row, $css, $variable, $file, $line, $description);
        }
        $body .= '</tbody>';

        $content = sprintf('<table class="neat-table">%s%s</table>', $head, $body);

        return $content;
    }

    /**
     * Dumps information about an array.
     *
     * @param array $array
     * @param bool  $recursive
     * @param int   $depth
     *
     * @return string
     */
    private function dumpArray(array & $array, $recursive, $depth)
    {
        $count = count($array);

        if (0 == $count) {
            return 'array(0)';
        }

        if (isset($array[$this->mark])) {
            return sprintf('array(%s) *RECURSION*', $count - 1);
        }

        $array[$this->mark] = true;
        $indent = str_repeat(' ' , $depth * 4);
        $output = sprintf('array(%s)', $count);
        $size = 0;

        foreach($array as $key => & $value) {
            if ($this->mark === $key) continue;

            if ($size > $this->size) {
                $output .= '...';
                break;
            } else {
                $output .= sprintf('%s%s    [%s] => ', PHP_EOL, $indent, $key);
                $output .= $recursive ? $this->dump($value, $recursive, $depth + 1) : $this->describe($value);
                $size++;
            }
        }

        $output .= sprintf('%s%s', PHP_EOL, $indent);
        unset($array[$this->mark]);

        return $output;
    }

    /**
     * Dumps information about an object.
     *
     * @param mixed $object
     * @param bool  $recursive
     * @param int   $depth
     *
     * @return string
     */
    private function dumpObject($object, $recursive, $depth)
    {
        $class = get_class($object);
        if ($this->objects->contains($object)) {
            $output = sprintf('object(%s) *RECURSION*', $class);
        } else {
            $this->objects->attach($object);
            $reflection = new ReflectionObject($object);
            $indent = str_repeat(' ', $depth * 4);
            $output = sprintf('object(%s)', $class);
            $size = 0;

            foreach ($reflection->getProperties() as $property) {
                if ($size > $this->size) {
                    $output .= '...';
                    break;
                } else {
                    $property->setAccessible(true);
                    $value = $property->getValue($object);

                    $output .= sprintf('%s%s    [%s] => ', PHP_EOL, $indent, $property->getName());
                    $output .= $recursive ? $this->dump($value, $recursive, $depth + 1) : $this->describe($value);
                    $size++;
                }
            }

            $output .= sprintf('%s%s', PHP_EOL, $indent);
            $this->objects->detach($object);
        }

        return $output;
    }
}