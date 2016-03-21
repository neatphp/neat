<?php
namespace Neat\Test\Controller\Fixture;

use Neat\Http\Response;

class Controller extends \Neat\Controller\Controller
{
    public function defaultAction()
    {
        return new Response;
    }
}