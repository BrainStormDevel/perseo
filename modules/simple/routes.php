<?php

use Slim\App;

$app->get('/this_is_a_test', \Modules\simple\Test::class);