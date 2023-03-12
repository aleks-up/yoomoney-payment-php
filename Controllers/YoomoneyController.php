<?php

class YoomoneyController
{
    private $config;
    private $api;

    public function __construct(Config $Config)
    {
        $this->config = $Config->get();
        $this->api = new YoomoneyApi(
            $this->config['yoomoney']['yoomoney_token'],
            $this->config['yoomoney']['yoomoney_wallet']
        );
    }

    // Отображение формы для оплаты заказа
    public function redirectToPaymentForm(Request $request)
    {
        $amount = $request->getGet()['amount'];
        $comment = $request->getGet()['comment'];

        // Формирование URL для отправки запроса на создание ссылки на оплату
        $payment_url = $this->api->getPaymentUrl($amount, $comment);
        // Отображение формы для оплаты заказа
        header("Location: {$payment_url}");
    }

    // Обработка уведомлений об оплате

    /**
     * @param Request $request
     * @return void
     */
    public function getHistory(Request $request)
    {
        $records = $request->getGet()['records'];
        $history_json = $this->api->getPaymentsHistory(array('records' => $records, 'type' => 'deposition', 'details' => 'true'));

        $response = new Response(200);
        $body = json_encode(
            ["status" => "ok", "message" => $history_json]
        );
        $response->withHeader()->withBody($body)->send();
    }
}
