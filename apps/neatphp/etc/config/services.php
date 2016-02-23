<?php
use Neat\Config\Config;
use Neat\Container;
use Neat\Container\Definition;
use Neat\Event\Dispatcher;
use Neat\Http\Request;
use Neat\Loader\FileLoader;
use Neat\Profiler\Profiler;
use Neat\Router\Router;
use Neat\Util\Timer;

return [
    Definition::singleton(Config::class, null, '@Neat\Parser\Json')
        ->setPlaceholders([
            'app_dir' => '@app:getBasedir',
            'app_namespace' => '@app:getNamespace',
        ]),

    Definition::singleton(Dispatcher::class),

    Definition::singleton(Profiler::class, null, '@app:isInDevMode')
        ->setTabs([
            '@Neat\Profiler\Tab\Files',
            '@Neat\Profiler\Tab\Locations',
            '@Neat\Profiler\Tab\Memory',
            '@Neat\Profiler\Tab\Time',
        ]),

    Definition::singleton(Request::class),

    Definition::singleton(Router::class)
        ->setRoutes([
            '/module/:module/controller/:controller/action/:action',
            '/controller/:controller/action/:action',
            '/action/:action',
        ]),

    Definition::singleton(FileLoader::class)
        ->setPlaceholders([
            'app_dir'    => '@app:getBasedir',
            'module'     => '@Neat\Http\Request:get:module:Module',
            'controller' => '@Neat\Http\Request:get:controller:Home',
        ])
        ->setLocations([
            'config'   => [
                '{{app_dir}}/etc/config'
            ],
            'template' => [
                '{{app_dir}}/src/{{module}}/View/Default',
                '{{app_dir}}/src/{{module}}/View/{{controller}}',
            ]
        ]),

    Definition::singleton(Timer::class),
];