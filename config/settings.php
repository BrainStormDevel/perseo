<?php

return [
    'settings' => [
        'determineRouteBeforeAppMiddleware' => true,
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
    ]/*,
	   'request' => \Slim\Http\Request::createFromEnvironment(
            \Slim\Http\Environment::mock(
				$reqServer
            )
        )*/
];