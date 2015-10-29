<?php
namespace Neat;

use Exception;
use Neat\Container\Container;
use Neat\Controller\Controller;
use Neat\Http\Request;
use Neat\Loader\ClassLoader;
use Neat\Profiler\Profiler;
use Neat\Util\Dumper;
use ReflectionClass;

require __DIR__ . '/Loader/FileLoader.php';
require __DIR__ . '/Loader/ClassLoader.php';

/**
 * Application.
 */
class App
{
	const MODE_DEV   = 'DEV';
	const MODE_STAGE = 'STAGE';
	const MODE_PRO   = 'PRO';

    /** @var string */
    protected $mode = self::MODE_DEV;

    /** @var string */
	protected $appDir;

    /** @var string */
    protected $webDir;

    /** @var string */
    protected $namespace;

    /** @var ClassLoader */
	protected $loader;

    /** @var Container */
	protected $services;

    /** @var array */
    private $errors = array (
        E_ERROR             => 'Error',
        E_WARNING           => 'Warning',
        E_PARSE             => 'Parsing Error',
        E_NOTICE            => 'Notice',
        E_CORE_ERROR        => 'Core Error',
        E_CORE_WARNING      => 'Core Warning',
        E_COMPILE_ERROR     => 'Compile Error',
        E_COMPILE_WARNING   => 'Compile Warning',
        E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
        E_USER_ERROR        => 'User Error',
        E_USER_WARNING      => 'User Warning',
        E_USER_NOTICE       => 'User Notice',
        E_STRICT            => 'Runtime Notice',
    );

    /**
     * Constructor.
     *
     * @param string $mode
     * @param string $appDir
     * @param string $webDir
     */
    public function __construct($mode, $appDir, $webDir)
    {
        $this->mode = $mode;
        $this->appDir = $appDir;
        $this->webDir = $webDir;

        $class = new ReflectionClass(get_class($this));
        $this->namespace = $class->getNamespaceName();

        switch ($this->mode) {
            case self::MODE_DEV:
                error_reporting(E_ALL | E_STRICT);
                ini_set('display_errors', 1);
                break;

            case self::MODE_STAGE:
            case self::MODE_PRO:
                error_reporting(0);
                ini_set('display_errors', 0);
                break;

            default:
                exit('Invalid application mode.');
        }
    }

    /**
     * Tells whether application is running in development mode.
     *
     * @return bool
     */
    public function isInDevMode()
    {
        return self::MODE_DEV == $this->mode;
    }

    /**
     * Tells whether application is running in staging mode.
     *
     * @return bool
     */
    public function isInStageMode()
    {
        return self::MODE_STAGE == $this->mode;
    }

    /**
     * Tells whether application is running in production mode.
     *
     * @return bool
     */
    public function isInProMode()
    {
        return self::MODE_PRO == $this->mode;
    }

    /**
     * Returns the working mode.
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Returns the base directory.
     *
     * @return string
     */
    public function getAppDir()
    {
        return $this->appDir;
    }

    /**
     * Returns the web directory.
     *
     * @return string
     */
    public function getWebDir()
    {
        return str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->webDir);
    }

    /**
     * Returns the config directory.
     *
     * @return string
     */
    public function getConfigDir()
    {
        return realpath($this->appDir . '/../etc/config');
    }

    /**
     * Returns the namespace.
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Returns the loader.
     *
     * @return ClassLoader
     */
    public function getLoader()
    {
        if (is_null($this->loader)) {
            $this->loader = new ClassLoader;
            $this->loader->setLocation($this->getNamespace(), [$this->appDir]);
            $this->loader->setLocation('Neat', [__DIR__]);
        }

    	return $this->loader;
    }

    /**
     * Returns the services.
     *
     * @param string $name
     *
     * @return Container|mixed
     */
    public function getServices($name = null)
    {
    	if (!$this->services) {
            $path = $this->getConfigDir() . '/services.php';
            $this->services = new Container(require $path);
            $this->services->set('app', $this);
            $this->services->set('Neat\Loader\ClassLoader', $this->getLoader());
        }

    	return isset($name) ? $this->services->get($name) : $this->services;
    }

    /**
     * Returns the controller.
     *
     * @param Request|null $request
     *
     * @return Controller
     */
    public function getController(Request $request = null)
    {
        if (is_null($request)) $request = $this->getServices('Neat\Http\Request');

        $controllerClassName = sprintf(
            '%s\%s\Controller\%s',
            $this->getNamespace(),
            $request->get('module'),
            $request->get('controller')
        );

        return $this->getServices($controllerClassName);
    }

    /**
     * Returns the profiler.
     *
     * @return Profiler
     */
    public function getProfiler()
    {
        return $this->getServices('Neat\Profiler\Profiler');
    }

    /**
     * Error handling.
     *
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param int    $errline
     * @param array  $errcontext
     *
     * @return void
     */
    public function handleError($errno, $errstr, $errfile, $errline, $errcontext)
    {
        $name = isset($this->errors[$errno]) ? $this->errors[$errno] : 'Unknown Error';
        $message = sprintf('Error in %s(%s) with message:%s%s.', $errfile, $errline, PHP_EOL, $errstr);
        $this->renderDebugTemplate($name, $message);
    }

    /**
     * Exception handling.
     *
     * @param Exception $exception
     *
     * @return void
     */
    public function handleException(Exception $exception)
    {
        $name = get_class($exception);
        $message = $exception->getMessage();
        $backtrace = $exception->getTrace();
        $this->renderDebugTemplate($name, $message, $backtrace);
    }

    /**
     * Runs the application.
     */
    public function run()
    {
        spl_autoload_register(array($this->getLoader(), 'autoload'));

        if (self::MODE_DEV == $this->mode) {
            set_error_handler(array($this, 'handleError'), error_reporting());
            set_exception_handler(array($this, 'handleException'));
        }

        /** @var Request $request */
        $request = $this->getServices('Neat\Router\Router')->route();
        $response = $this->getController($request)->execute();
        $response->send();

        if (self::MODE_DEV == $this->mode) {
            echo $this->getProfiler()->render($response->body);
        } else {
            echo $response->body;
        }
    }

    /**
     * Debug information.
     *
     * @param string $name
     * @param string $message
     * @param array $backtrace
     *
     * @return string
     */
    protected function renderDebugTemplate($name, $message, array $backtrace = null)
    {
        if (is_null($backtrace)) {
            $backtrace = debug_backtrace();
            array_shift($backtrace);
        }

        $dumper = new Dumper;
        $path = __DIR__ . '/etc/template/';
        $webDir = $this->getWebDir();

        if ('cli' == substr(PHP_SAPI, 0, 3)) {
            $path .= 'debug.php';
            $backtrace = $dumper->dumpBacktrace($backtrace);
        } else {
            $path .= 'debug.phtml';
            $message = nl2br($message);
            $backtrace = $dumper->formatBacktrace($backtrace);
            $records = $this->getProfiler()->getDebugRecords();
            $debug = $dumper->formatDebugRecords($records);
        }

        require $path;
        exit;
    }
}