<?php
namespace Neat\Controller;

use Neat\Base\Component;
use Neat\Http\Response;
use ReflectionClass;

/**
 * Controller takes information from the HTTP request and creates an HTTP response.
 *
 * @property \Neat\Http\Request          request
 * @property \Neat\Loader\TemplateLoader templateLoader
 * @property \Neat\Loader\PluginLoader   pluginLoader
 */
class Controller extends Component
{
    /** @var callable */
    private $templateEngine;

    /**
     * Returns the template engine.
     *
     * @return callable
     */
    public function getTemplateEngine()
    {
        return $this->templateEngine;
    }

    /**
     * Sets the template engine.
     *
     * @param callable $callback
     *
     * @return Controller
     */
    public function setTemplateEngine(callable $callback)
    {
        $this->templateEngine = $callback;

        return $this;
    }

    /**
     * Executes a requested action.
     *
     * @param string $action
     *
     * @return Response
     */
    public function execute($action = null)
    {
        if (is_null($action)) $action = $this->request->get('action');

        $event = $this->dispatchEvent('controller.pre_execute', ['action' => $action]);

        $method = $event['action'] . 'Action';
        $response = $this->$method();

        $this->dispatchEvent('controller.post_execute', ['action' => $action]);

        return $response;
    }

    /**
     * Returns template file name.
     *
     * @param string $ext
     *
     * @return string
     */
    protected function getTemplate($ext = 'html')
    {
        $request = $this->request;
        $action = $request->get('action');
        $template = sprintf('%s.%s', $action, $ext);

        return $template;
    }

    /**
     * Returns a plugin.
     *
     * @param string $name
     * @param string $superclass
     * @param array  $args
     *
     * @return mixed
     */
    protected function getPlugin($name, $superclass, array $args = [])
    {
        $class = new ReflectionClass($this->pluginLoader->load($name, $superclass));
        $plugin = $class->newInstanceArgs($args);

        return $plugin;
    }

    /**
     * Renders the template.
     *
     * @param string   $template
     * @param array    $args
     * @param Response $response
     *
     * @return Response
     */
    protected function render($template, array $args = [], Response $response = null)
    {
        if (is_null($response)) $response = new Response;

        $callback = $this->getTemplateEngine();
        if (isset($callback)) {
            $path = $this->templateLoader->locate($template, 'view');
            $response->body = $callback($path, $args);

            return $response;
        }

        $search = [];
        $replace = [];
        foreach ($args as $key => $value) {
            $search[] = sprintf('{{%s}}', (string)$key);
            $replace[] = $value;
         }

        $data = $this->templateLoader->load($template, 'view');
        $response->body = str_replace($search, $replace, $data);

        return $response;
    }
}