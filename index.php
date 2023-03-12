<?php

// Подключение файлов
require_once 'Config/Config.php';
require_once 'Lib/YoomoneyApi.php';
require_once 'Lib/Http/Request.php';
require_once 'Lib/Http/Response.php';
require_once 'Models/PaymentModel.php';
require_once 'Controllers/PaymentController.php';
require_once 'Controllers/YoomoneyController.php';


// Определение конфигураций
$config = new Config('config.ini');
$request = new Request($_POST, $_GET, $_SERVER);

// Определение маршрутов
$routes = array(
    '/payment/yoomoney' => 'YoomoneyController@redirectToPaymentForm',
    '/payment/yoomoney/history' => 'YoomoneyController@getHistory',
    '/payment/health' => 'PaymentController@health',
);

// Обработка текущего запроса
$request_uri = $_SERVER['REQUEST_URI'];
$parsed_url = parse_url($request_uri);
$route = $parsed_url['path'];
parse_str($parsed_url['query'], $query_params);

// Проверка существования маршрута
if (array_key_exists($route, $routes)) {
    // Разбор строки контроллера и метода
    list($controller_name, $method_name) = explode('@', $routes[$route]);

    // Создаем экземпляр контроллера с конфигурациями приложения
    $controller = new $controller_name($config);

    // Вызываем метод контроллера с текущим зарпосом в параметре
    $controller->$method_name($request);
} else {
    (new Response(404))->withHeader()->send();
}
