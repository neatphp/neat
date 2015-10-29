<?php
namespace Neat\Config;

use Neat\Data\Data;
use Neat\Loader\FileLoader;

/**
 * Config holds application settings.
 */
class Config extends Data
{
    /** @var FileLoader */
    private $fileLoader;

    /**
     * Constructor.
     *
     * @param FileLoader $fileLoader
     */
    public function __construct(FileLoader $fileLoader)
    {
        parent::__construct(true, false);

        $this->fileLoader = $fileLoader;
    }

    /**
     * Returns the file loader.
     *
     * @return FileLoader
     */
    public function getFileLoader()
    {
        return $this->fileLoader;
    }

    /**
     * Sets a branch.
     *
     * @param string $option
     * @param string $file
     *
     * @return Config
     */
    public function setBranch($option, $file)
    {
        $this->set($option, function () use ($option, $file) {
            $branch = new Config($this->fileLoader);
            $branch->loadFile($file);

            return $branch;
        });

        return $this;
    }

    /**
     * Loads a config file.
     *
     * @param string $file
     *
     * @return Config
     */
    public function loadFile($file)
    {
        $this->setValues($this->fileLoader->load($file, 'config'));

        return $this;
    }
}