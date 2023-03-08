<?php

class Config
{
    private $data;

    public function __construct($configFilePath)
    {
        $this->data = parse_ini_file($configFilePath, true);
    }

    public function get()
    {
        return $this->data;
    }

    public function getYoomoneyConfig()
    {
        return $this->data['yoomoney'];
    }

    public function getDatabaseConfig()
    {
        return $this->data['database'];
    }
}