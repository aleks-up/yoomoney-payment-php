<?php

class YoomoneyApi
{
    private $_token;
    private $_url;
    private $_wallet;

    function __construct($_token, $_wallet)
    {
        $this->_token = $_token;
        $this->_wallet = $_wallet;
        $this->_url = 'https://yoomoney.ru/';
    }

    //array('records'=>'3', 'type'=>'deposition', 'details'=>'true')
    private function sendRequest($method, array $content = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->_url . $method);
        curl_setopt($ch, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($content));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($content));

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Host: yoomoney.ru',
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer ' . $this->_token
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, 1);
    }

    public function getPaymentsHistory(array $params = [])
    {
        return $this->sendRequest('/api/operation-history', $params);
    }

    public function getPaymentUrl($amount, $comment)
    {
        $writer = 'seller';
        $targets = $comment;
        $destination = $comment;
        $default_sum = $amount;
        $button_text = 12;
        $quickpay = 'shop';
        $account = $this->_wallet;

        $url = "https://yoomoney.ru/quickpay/shop-widget?";
        $url .= "writer=$writer";
        $url .= "&targets=$targets";
        $url .= "&destination=$destination";
        $url .= "&targets-hint=";
        $url .= "&default-sum=$default_sum";
        $url .= "&button-text=$button_text";
        $url .= "&hint=";
        $url .= "&successURL=";
        $url .= "&quickpay=$quickpay";
        $url .= "&account=$account";

        return $url;
    }


}
