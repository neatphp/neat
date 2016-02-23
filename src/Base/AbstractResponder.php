<?php
namespace Neat\Base;

use Neat\Widget\AbstractWidget;
use Psr\Http\Message\ResponseInterface as Response;
use ReflectionClass;

/**
 * ADR responder.
 *
 * @property       \Psr\Http\Message\ResponseInterface response
 * @property-read  \Neat\Data\Data data                params
 * @property-read  \Neat\Loader\FileLoader             fileLoader
 * @property-read  \Neat\Loader\PluginLoader           pluginLoader
 */
abstract class AbstractResponder extends Component
{
    /**
     * @return Response
     */
    abstract public function __invoke();

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
     * Renders the template.
     *
     * @param string $template
     *
     * @return Response
     */
    protected function render($template)
    {
        $search = [];
        $replace = [];
        foreach ($this->params as $key => $value) {
            $search[] = sprintf('{{%s}}', (string)$key);
            $replace[] = $value;
        }

        $data = $this->fileLoader->load($template, 'template');
        $this->response->getBody()->write(str_replace($search, $replace, $data));

        return $this->response;
    }
}