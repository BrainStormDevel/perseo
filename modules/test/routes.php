<?php

use Slim\App;

$app->get('/test', \Modules\test\Main::class);