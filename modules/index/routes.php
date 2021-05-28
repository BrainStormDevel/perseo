<?php

use Slim\App;
use Modules\login\MiddleWare\CheckLogin;

//$app->get('/', \Modules\index\Main::class)->add(CheckLogin::class);
$app->get('/', \Modules\index\Main::class);
$app->get('/ciccio', \Modules\index\Main::class);