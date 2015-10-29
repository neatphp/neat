<?php
use Neat\Router\Router;
use Neat\Config\Config;
use Neat\Container;
use Neat\Container\Definition;
use Neat\Event\Dispatcher;
use Neat\Http\Request;
use Neat\Loader\TemplateLoader;
use Neat\Profiler\Profiler;
use Neat\Util\Timer;

return [
    Definition::singleton(Config::class),

    Definition::singleton(Dispatcher::class),

    Definition::singleton(Profiler::class, null, '@app:isInDevMode')
        ->addTab('@Neat\Profiler\Tab\Files')
        ->addTab('@Neat\Profiler\Tab\Locations')
        ->addTab('@Neat\Profiler\Tab\Memory')
        ->addTab('@Neat\Profiler\Tab\Time'),

    Definition::singleton(Request::class),

    Definition::singleton(Router::class)->properties(true)
        ->addRoute('/module/:module/controller/:controller/action/:action'),

    Definition::singleton(TemplateLoader::class, '@app:getAppDir')
        ->setLocation('*', [
            '{{module}}/View/{{controller}}',
        ]),

    Definition::singleton(Timer::class),
];