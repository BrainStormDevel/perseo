<?php
$app->get('/test[/]', function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {
    try {
        /*$dash = array();
        $menu = array();
        $dash['title'] = 'Dashboard';
        $dash['icon'] = 'fas fa-tachometer-alt';
        $dash['location'] = '/admin/';

        $menu[0] = $dash;
        
        $prova = $container->get('menu.admin');
        var_dump($prova);*/

        $test = '{"title":"Admin","l_search":"Cerca...","l_dashboard":"Dashboard","l_profile":"Profilo","l_logout":"Esci"}';
        $mioarray = json_decode($test, true);
        $newarray['body'] = $mioarray;
        echo json_encode($newarray);


        print_r($container->get('modules.name'));

        //print_r($menu);
        //echo json_encode($dash);
        //echo "modulo test funzionante";
        //$data = $container->get('db')->select("admins", "*");
        //var_dump($data);
    } catch (Exception $e) {
        die("PerSeo ERROR : " . $e->getMessage());
    }
});