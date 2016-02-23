<?php
namespace Neat\Config;

use Neat\Loader\FileLoader;
use Neat\Parser\ParserInterface;

/**
 * Config holds application settings.
 */
class Config
{
    /** @var FileLoader */
    private $fileLoader;

    /** @var ParserInterface */
    private $parser;

    /** @var array */
    private $placeholders = [];

    /** @var array */
    private $settings = [];

    /**
     * Constructor.
     *
     * @param FileLoader      $fileLoader
     * @param ParserInterface $parser
     */
    public function __construct(FileLoader $fileLoader, ParserInterface $parser)
    {
        $this->fileLoader = $fileLoader;
        $this->parser     = $parser;
    }

    /**
     * Loads a config file.
     *
     * @param string $file
     *
     * @return self
     */
    public function loadFile($file)
    {
        $content = $this->fileLoader->load($file, 'config');
        if ($this->placeholders) {
            $search  = array_keys($this->placeholders);
            $replace = array_values($this->placeholders);
            $content = str_replace($search, $replace, $content);
        }

        $values = $this->parser->parse($content);
        $this->settings = array_merge($this->settings, $values);

        return $this;
    }

    /**
     * Tells whether an offset path exists.
     *
     * @param string $path
     *
     * @return bool
     */
    public function has($path)
    {
        $this->assertPathIsString($path);
        $this->assertPathIsNotEmpty($path);

        $data    = $this->settings;
        $path    = trim($path, '.');
        $offsets = explode('.', $path);

        foreach ($offsets as $offset) {
            if (!isset($data[$offset])) {
                return false;
            }

            $data = $data[$offset];
        }

        return true;
    }

    /**
     * Retrieves value of an offset path.
     *
     * @param string $path
     *
     * @return string
     *
     * @throws Exception\OutOfBoundsException
     */
    public function get($path)
    {
        $this->assertPathIsString($path);
        $this->assertPathIsNotEmpty($path);

        $data    = $this->settings;
        $path    = trim($path, '.');
        $offsets = explode('.', $path);

        foreach ($offsets as $offset) {
            if (!isset($data[$offset])) {
                $msg = sprintf('Offset path "%s" does not exist.', $path);
                throw new Exception\OutOfBoundsException($msg);
            }

            $data = $data[$offset];
        }

        return $data;
    }

    /**
     * Retrieves the file loader.
     *
     * @return FileLoader
     */
    public function getFileLoader()
    {
        return $this->fileLoader;
    }

    /**
     * Retrieves the parser.
     *
     * @return ParserInterface
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * Retrieves placeholders.
     *
     * @return array
     */
    public function getPlaceholders()
    {
        return $this->placeholders;
    }

    /**
     * Sets placeholders.
     *
     * @param array $placeholders
     *
     * @return self
     */
    public function setPlaceholders(array $placeholders)
    {
        foreach ($placeholders as $name => $value) {
            $this->setPlaceholder($name, $value);
        }

        return $this;
    }

    /**
     * Sets a placeholder.
     *
     * @param string $name
     * @param string $value
     *
     * @return self
     */
    public function setPlaceholder($name, $value)
    {
        $this->placeholders['{{' . $name . '}}'] = $value;

        return $this;
    }

    /**
     * @param mixed $path
     *
     * @throws Exception\InvalidArgumentException
     */
    private function assertPathIsString($path)
    {
        if (!is_string($path)) {
            $msg = 'Path should not be a string.';
            throw new Exception\InvalidArgumentException($msg);
        }
    }

    /**
     * @param string $path
     *
     * @throws Exception\UnexpectedValueException
     */
    private function assertPathIsNotEmpty($path)
    {
        if (empty($path) || '.' === $path) {
            $msg = 'Path should not be empty.';
            throw new Exception\UnexpectedValueException($msg);
        }
    }
}