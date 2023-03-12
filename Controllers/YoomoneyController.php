<?php

class YoomoneyController
{
    private $config;
    private $api;
    public string $payment_system = 'yoomoney';

    public function __construct()
    {
        $this->config = (new Config('config.ini'))->get();
        $this->api = new YoomoneyApi(
            $this->config[$this->payment_system]['token'],
            $this->config[$this->payment_system]['wallet']
        );
    }

    /**

    Метод для перенаправления на страницу оплаты заказа.

    @param Request $request Объект, содержащий данные запроса.

    @return void
     */
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
     * Получает историю платежей Юмани
     *
     * @param Request $request
     * @return void
     */
    public function getHistory(Request $request)
    {
        $records = $request->getGet()['records'];
        $data = $this->api->getPaymentsHistory(array('records' => $records, 'type' => 'deposition', 'details' => 'true'));


        $response = new Response(200);
        $body = json_encode(
            ["status" => "ok", "message" => $data]
        );
        $response->withHeader()->withBody($body)->send();

    }

    /**
     * Синхронизирует платежи с ЮMoney и сохраняет их в базу данных.
     * Метод использует свойство $payment_system для определения системы оплаты, которую необходимо синхронизировать.
     *
     * @return void
     * @throws Exception Если произошла ошибка при выполнении запроса к базе данных
     */
    public function _syncPayments(): void
    {
        # получаем последние платежи с юмани
        $data = $this->api->getPaymentsHistory(array('records' => 3, 'type' => 'deposition', 'details' => 'true'));

        # сохраняем платежи в БД
        $paymentDao = new PaymentDao();
        foreach ($data['operations'] as $operation) {
            $payment = Payment::fromYooMoney($operation);

            $paymentDao->save_payment($payment);
        }
    }

    public function checkPayment(Request $request)
    {
        # актуализируем платежи в БД
        $this->_syncPayments();

        # валидируем параметры
        $val = $request->validateRequiredParams(['user_id', 'card_last_digits']);

        # Последние 4 цифры номера карты
        $card_last_digits = $request->getGet()['card_last_digits'];
        $user_id = $request->getGet()['user_id'];
        $order_id = $request->getGet()['order_id'];

        $paymentDao = new PaymentDao();
        $payment_id = $paymentDao->findNewPaymentByCardNumber($card_last_digits);
        if ($payment_id) {
            $paymentDao->assign_payment_for_user($payment_id, $user_id, $order_id);
        }
        # Ищем платеж без номера заказа
        $response = new Response(200);
        $body = json_encode(
            ["status" => "ok", "message" => [
                "payment_id" => $payment_id
            ]]
        );
        $response->withHeader()->withBody($body)->send();
    }
}
