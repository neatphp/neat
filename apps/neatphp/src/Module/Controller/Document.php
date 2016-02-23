<?php
namespace Demo\Module\controller;

use Neat\Controller\Controller;

/**
 * Class Index
 */
class Index extends Controller
{
	public function defaultAction()
	{
        $this->debug($this->request->getParams);

        return $this->render($this->getTemplate());
    }
}