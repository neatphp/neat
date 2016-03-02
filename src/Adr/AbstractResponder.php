<?php
namespace Neat\Adr;

use Neat\Base\Component;
use Neat\Widget\AbstractWidget;
use Psr\Http\Message\ResponseInterface as Response;
use ReflectionClass;

/**
 * ADR responder.
 *
 * @property       \Psr\Http\Message\ResponseInterface response
 * @property       \Neat\Adr\Payload                   payload
 * @property-read  \Neat\Loader\FileLoader             fileLoader
 * @property-read  \Neat\Loader\PluginLoader           pluginLoader
 */
class AbstractResponder extends Component
{
    /**
     * Method will be called when a script tries to call responder as a function.
     */
    public function __invoke()
    {
        $method = $this->getPayloadMethod();
        $this->assertPayloadMethodExists($method);
        $this->$method();
    }

    /**
     * Retrieves the payload method.
     *
     * @return string
     */
    protected function getPayloadMethod()
    {
        $status = $this->payload->getStatus();
        $method = strtolower($status);
        $method = ucwords($method, '_');
        $method = str_replace('_', '', $method);
        $method = lcfirst($method);

        return $method;
    }

    /**
     * Renders the template.
     *
     * @param string $template
     *
     * @return Response
     */
    protected function render($template)
    {
        $search  = [];
        $replace = [];
        foreach ($this->payload as $key => $value) {
            $search[]  = sprintf('{{%s}}', (string)$key);
            $replace[] = $value;
        }

        $data = $this->fileLoader->load($template, 'template');
        $this->response->getBody()->write(str_replace($search, $replace, $data));

        return $this->response;
    }

    /**
     * Creates a widget.
     *
     * @param string $name
     * @param array  $args
     *
     * @return AbstractWidget
     */
    protected function createWidget($name, array $args = [])
    {
        $class = new ReflectionClass($this->pluginLoader->load($name, 'Neat\Widget\AbstractWidget'));
        $widget = $class->newInstanceArgs($args);

        return $widget;
    }

    /**
     * @param string $method
     *
     * @throws Exception\BadMethodCallException
     */
    private function assertPayloadMethodExists($method)
    {
        if (!method_exists($this, $method)) {
            $msg = sprintf('Payload method "%s" does not exists in responder "%s".', $method, get_class($this));
            throw new Exception\BadMethodCallException($msg);
        }
    }
}