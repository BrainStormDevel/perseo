<?php
$app->get('/admin/users[/]', function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {
    try {
        echo "ok";
    } catch (Exception $e) {
        die("PerSeo ERROR : " . $e->getMessage());
    }
})->add(new \login\Controllers\CheckLogin($container, 'admins'));