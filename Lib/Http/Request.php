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
}
