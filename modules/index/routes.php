<?php

use Slim\App;

$app->get('/', \Modules\index\Views\Main::class);