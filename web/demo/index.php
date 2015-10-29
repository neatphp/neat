<?php
require __DIR__ . '/../../src/App.php';
require __DIR__ . '/../../apps/demo/src/App.php';

use Demo\App;

$app = new App(App::MODE_DEV, __DIR__);
$app->run();