<?php

class YoomoneyController
{
    private $config;

    public function __construct(Config $Config)
    {
        $this->config = $Config->get();
    }

    // Отображение формы для оплаты заказа
    public function redirectToPaymentForm()
    {
        $amount = $_GET['amount'] ?? null;
        $comment = $_GET['comment'] ?? null;

        $_wallet = $this->config['yoomoney']['yoomoney_wallet'];
        $_token = $this->config['yoomoney']['yoomoney_token'];
        $comment = 'Оплата заказа 123'; // описание заказа

        // Формирование URL для отправки запроса на создание ссылки на оплату
        $api = new YoomoneyApi($_token, $_wallet);
        $payment_url = $api->getPaymentUrl($amount, $comment);
        // Отображение формы для оплаты заказа
        header("Location: {$payment_url}");
    }

    // Обработка уведомлений об оплате
    public function health(): void
    {
        $response = new Response(200);
        $body = json_encode(
            ["status" => "ok", "message" => "Application is running."]
        );
        $response->withHeader()->withBody($body)->send();
    }

}
