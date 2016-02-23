<?php
require __DIR__ . '/../../../src/App.php';
require __DIR__ . '/../src/App.php';

use Neatphp\App;

$app = new App(App::MODE_DEV);
$app->run();