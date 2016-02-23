<?php
namespace Demo;

/**
 * Demo application.
 */
class App extends \Neat\App
{
    /**
     * Constructor.
     *
     * @param string $mode
     * @param string $webDir
     */
    public function __construct($mode, $webDir)
    {
        parent::__construct($mode, __DIR__, $webDir);
    }
}