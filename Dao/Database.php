<?php

class Database
{
    private $config;
    private $conn;

    function __construct()
    {
        $this->config = (new Config('config.ini'))->get();

        // установка соединения с базой данных

        $this->conn = new PDO(
            "mysql:host={$this->config['database']['host']};dbname={$this->config['database']['database']}",
            $this->config['database']['username'],
            $this->config['database']['password']
        );

        if (!$this->conn) {
            $error = $this->conn->errorInfo();
            die("Connection failed: " . $error[2]);
        }

    }


    // проверка соединения

    public function get_error()
    {
        $error_info = $this->conn->errorInfo();
        return $error_info[2];
    }

    public function select($table, $columns = "*", $where = null, $order_by = null, $limit = null)
    {
        // формирование запроса SELECT
        $sql = "SELECT $columns FROM $table";
        if ($where != null) {
            $sql .= " WHERE $where";
        }
        if ($order_by != null) {
            $sql .= " ORDER BY $order_by";
        }
        if ($limit != null) {
            $sql .= " LIMIT $limit";
        }
        // выполнение запроса
        $stmt = $this->conn->query($sql);
        if (!$stmt) {
            $error = $this->conn->errorInfo();
            die("Error: " . $sql . "<br>" . $error[2]);
        }

        // возвращение результата в виде ассоциативного массива
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    function insert($table, $data)
    {
        // формирование запроса INSERT
        $keys = array();
        $values = array();
        foreach ($data as $key => $value) {
            $keys[] = $key;
            $values[] = $value;
        }
        $sql = "INSERT INTO $table (" . implode(",", $keys) . ") VALUES (" . rtrim(str_repeat("?,", count($values)), ",") . ")";
        $stmt = $this->conn->prepare($sql);
        $result = $stmt->execute($values);

        if ($result) {
            return $this->conn->lastInsertId();
        } else {
            $error = $stmt->errorInfo();
            die("Error: " . $error[2]);
        }
    }


    function update($table, $data, $where = null)
    {
        // формирование запроса UPDATE
        $sets = array();
        foreach ($data as $key => $value) {
            $sets[] = "$key=:value_$key";
        }
        $sql = "UPDATE $table SET " . implode(",", $sets);
        if ($where != null) {
            $sql .= " WHERE $where";
        }

        // подготовка и выполнение запроса
        $stmt = $this->conn->prepare($sql);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":value_$key", $value);
        }
        if ($stmt->execute()) {
            return $stmt->rowCount();
        } else {
            $error = $stmt->errorInfo();
            die("Error: " . $sql . "<br>" . $error[2]);
        }
    }

}
