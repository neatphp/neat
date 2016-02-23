<?php
namespace Neatphp;

/**
 * Demo application.
 */
class App extends \Neat\App
{
    /**
     * Constructor.
     *
     * @param string $mode
     */
    public function __construct($mode)
    {
        parent::__construct($mode, realpath(__DIR__ . '/..'));
    }
}