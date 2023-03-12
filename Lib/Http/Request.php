<?php

class Request
{
    private $post;
    private $get;
    private $server;
    private $headers;

    public function __construct($post, $get, $server)
    {
        $this->post = $post;
        $this->get = $get;
        $this->server = $server;
        $this->headers = $this->getHeaders();
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

    public function getHeader($name)
    {
        $name = str_replace('-', '_', strtoupper($name));
        $key = 'HTTP_' . $name;
        return isset($this->headers[$key]) ? $this->headers[$key] : null;
    }

    private function getHeaders()
    {
        $headers = [];
        foreach ($this->server as $name => $value) {
            if (strpos($name, 'HTTP_') === 0) {
                $headers[$name] = $value;
            }
        }

        return $headers;
    }

    /**
     * Проверяет наличие и корректность заголовка X-Api-Secret в текущем запросе
     */
    public function checkApiSecret()
    {
        $config = (new Config('config.ini'))->get();
        $headerValue = $this->getHeader('x-api-key');

        if (empty($headerValue) || $headerValue !== $config['api']['x-api-key']) {
            $message = ($headerValue === null) ? 'Missing x-api-key header' : 'Invalid x-api-key header';
            $body = json_encode(['status' => 'error', 'message' => $message]);
            (new Response(401))->withHeader()->withBody($body)->send();
            exit();
        }
    }

}
