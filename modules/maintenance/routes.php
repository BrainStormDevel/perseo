<?php

use Slim\App;

$app->get('/maintenance', \Modules\maintenance\Main::class);