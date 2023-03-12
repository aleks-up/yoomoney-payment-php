<?php

class PaymentController
{

    public function health(): void
    {
        $response = new Response(200);
        $body = json_encode(
            ["status" => "ok", "message" => "Application is running."]
        );
        $response->withHeader()->withBody($body)->send();
    }

}
