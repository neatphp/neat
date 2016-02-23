<?php
namespace Neatphp\Module\Controller;

use Neat\Controller\Controller;

class Home extends Controller
{
	public function indexAction()
	{
        $args = [
            'baseUrl' => dirname($this->request->getBaseUrl()),
        ];

        return $this->render($this->getTemplate(), $args);
    }
}