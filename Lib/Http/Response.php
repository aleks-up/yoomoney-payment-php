<?php


class Response
{
    private $status;
    private $headers = array();
    private $body;

    public function __construct($status, $body = '')
    {
        $this->status = $status;
        $this->body = $body;
    }

    public function withHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function withStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function withBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function send()
    {
        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }

        echo $this->body;
    }
}
