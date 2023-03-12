<?php

class Request
{
    private $post;
    private $get;
    private $server;

    public function __construct($post, $get, $server)
    {
        $this->post = $post;
        $this->get = $get;
        $this->server = $server;
    }

    public function getPost()
    {
        return $this->post;
    }

    public function getGet()
    {
        return $this->get;
    }

    public function getServer()
    {
        return $this->server;
    }

    public function validateRequiredParams($required_params)
    {
        $get_params = $this->getGet();
        foreach ($required_params as $param) {
            if (!isset($get_params[$param]) || empty($get_params[$param])) {
                $response = new Response(400);
                $body = json_encode(
                    ["status" => "error", "message" => "Необходимо передать параметр {$param}."]
                );
                $response->withHeader()->withBody($body)->send();
                return false;
            }
        }
        return true;
    }

}
