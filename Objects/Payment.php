<?php

class Payment
{
    public $id;
    public $payment_system;
    public $operation_id;
    public $amount;
    public $title;
    public $datetime;
    public $_user_id;
    public $_order_id;

    /**
    Создает экземпляр класса Payment из данных ЮMoney.
    @param array $operation Массив данных о платеже, полученных от сервиса ЮMoney.
    @return Payment Экземпляр класса Payment, созданный на основе данных о платеже.
     */
    public static function fromYooMoney($operation)
    {
        $payment = new Payment();
        $payment->payment_system = 'yoomoney';
        $payment->operation_id = $operation['operation_id'];
        $payment->amount = $operation['amount'];
        $payment->title = $operation['title'];
        $payment->datetime = (new DateTime($operation['datetime']))->format('Y-m-d H:i:s');
        $payment->_user_id = null;
        $payment->_order_id = null;
        return $payment;
    }


}
