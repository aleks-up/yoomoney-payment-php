<?php

class PaymentController
{
    private $config;

    public function __construct(Config $Config)
    {
        $this->config = $Config->getYoomoneyConfig();
    }

    // Отображение формы для оплаты заказа
    public function index()
    {
        // Получение данных для формы из модели
        $model = new PaymentModel();
        $amount = 100.00; // сумма заказа
        $currency = 'RUB'; // валюта заказа
        $description = 'Оплата заказа #123'; // описание заказа

        // Формирование URL для отправки запроса на создание ссылки на оплату
        $api = new YoomoneyApi("","");
        $action_url = $api->getPaymentUrl($amount, $currency);

        // Отображение формы для оплаты заказа
        include 'Views/payment_view.php';
    }

    // Обработка уведомлений об оплате
    public function notification()
    {
        // Получение данных об оплате из POST-запроса
        $data = $_POST;
        echo "public function notification()";
        exit;
        // Проверка подписи запроса
        $api = new YooMoney_API($config['yoomoney_shop_id'], $config['yoomoney_secret_key']);
        $valid = $api->check_payment_notification($data);

        // Обновление статуса платежа в базе данных
        $model = new Payment_Model();
        $payment_id = $data['label'];
        $status = $data['status'];
        $model->update_payment_status($payment_id, $status);

        // Отправка подтверждения обработки уведомления
        if ($valid) {
            echo 'OK';
        } else {
            echo 'ERROR';
        }
    }

    // Обработка успешной оплаты
    public function success()
    {
        // Получение информации о платеже из GET-запроса
        $data = $_GET;
        echo "public function success()";
        exit;
        // Получение информации о платеже из базы данных
        $model = new Payment_Model();
        $payment_id = $data['label'];
        $payment_info = $model->get_payment_info($payment_id);

        // Отображение страницы с информацией об оплате
        include 'Views/payment_success.php';
    }
}
