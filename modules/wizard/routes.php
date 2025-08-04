<?php

use Slim\App;

$app->get('/wizard[/]', \Modules\wizard\Views\Main::class);
$app->post('/wizard/test[/]', \Modules\wizard\Controllers\Test::class);
$app->post('/wizard/install[/]', \Modules\wizard\Controllers\Install::class);