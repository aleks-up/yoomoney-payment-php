<?php

// Подключение файлов
require_once 'Config/Config.php';
require_once 'Lib/YoomoneyApi.php';
require_once 'Lib/Http/Response.php';
require_once 'Models/PaymentModel.php';
require_once 'Controllers/PaymentController.php';


// Определение конфигураций
$Config = new Config('config.ini');

// Определение маршрутов
$routes = array(
    '/payment/' => 'PaymentController@index',
    '/payment/index' => 'PaymentController@index',
    '/payment/notification' => 'PaymentController@notification',
    '/payment/success' => 'PaymentController@success',
);

// Обработка текущего запроса
$request_uri = $_SERVER['REQUEST_URI'];
$route = str_replace('/index.php', '', $request_uri);
$route = str_replace('?', '', $route);


if (array_key_exists($route, $routes)) {
    $controller_action = $routes[$route];
    list($controller_name, $action_name) = explode('@', $controller_action);
    $controller = new $controller_name($Config);
    try {
        $controller->$action_name();
    } catch (Exception $e) {
        error_log($e->getMessage(), 3, 'exception.log');
    }

} else {
    $response = new Response(404);
    $response->withHeader('Content-Type', 'application/json')
        ->send();
}
