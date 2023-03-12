<?php

class PaymentDao extends Database
{


    /**
     * Сохраняет информацию об оплате в базу данных.
     *
     * @param Payment $payment экземпляр класса Payment
     *
     * @return bool|int Возвращает true при успешном сохранении записи в базу данных, false если запись уже существует,
     *                  или выбрасывает исключение с сообщением об ошибке при ошибке выполнения запроса
     * @throws Exception Если произошла ошибка при выполнении запроса к базе данных
     */
    public function save_payment(Payment $payment)
    {
        $db = new Database();
        $data = array(
            "payment_system" => $payment->payment_system,
            "operation_id" => $payment->operation_id,
            "amount" => $payment->amount,
            "title" => $payment->title,
            "datetime" => $payment->datetime,
            "_user_id" => $payment->_user_id,
            "_order_id" => $payment->_order_id
        );

        // проверка на существование записи с таким operation_id
        $existing_payment = $db->select("payments", "*", "operation_id='$payment->operation_id'");
        if (count($existing_payment) > 0) {
            // запись уже существует
            return false;
        }

        // вызов метода insert
        $result = $db->insert("payments", $data);
        if ($result === false) {
            // ошибка при выполнении запроса
            throw new Exception("Failed to save payment: " . $db->get_error());
        }

        return $result;
    }

    /**
     * Ищет последний неучтенный платеж в базе данных по последним цифрам номера карты.
     *
     * @param string $card_last_digits Последние 4 цифры номера карты
     * @return array Возвращает массив с данными найденного платежа, или пустой массив, если платеж не найден
     * @throws Exception Если произошла ошибка при выполнении запроса к базе данных
     */
    public function findNewPaymentByCardNumber($card_last_digits): int|null
    {
        $db = new Database();
        $new_payment_by_card = $db->select(
            'payments',
            'id',
            "RIGHT(title, 8) like '%{$card_last_digits}%' and _user_id is null",
            'id desc',
            '1'
        );
        // проверяем, найден ли платеж, и возвращаем его ID или null
        if (!empty($new_payment_by_card)) {
            return $new_payment_by_card[0]['id'];
        } else {
            return null;
        }
    }

    /**
     * Присваивает платеж конкретному пользователю и заказу.
     *
     * @param int $payment_id ID платежа, который необходимо присвоить пользователю и заказу
     * @param int $user_id ID пользователя, которому присваивается платеж
     * @param int $order_id ID заказа, который оплачивается платежом
     * @return bool Возвращает true, если присвоение платежа прошло успешно, false в случае ошибки
     * @throws Exception Если произошла ошибка при выполнении запроса к базе данных
     */
    public function assign_payment_for_user($payment_id, $user_id, $order_id)
    {
        $db = new Database();

        $user_data = array(
            "_user_id" => $user_id,
            "_order_id" => $order_id,
        );

        $where = "id={$payment_id}";

        $result = $db->update("payments", $user_data, $where);
        return ($result !== false);
    }


}
