<?php
$app->get('/test[/]', function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {
    try {
        //$data = $container->get('db')->select("admins", "*");
        //var_dump($data);
    } catch (Exception $e) {
        die("PerSeo ERROR : " . $e->getMessage());
    }
});