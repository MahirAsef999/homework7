<?php

namespace app\models;

abstract class Model {
    protected $table;

    // Find all records from the table
    public function findAll() {
        $query = "SELECT * FROM {$this->table}";
        return $this->query($query);
    }

    // Create a PDO connection
    private function connect() {
        $dsn = "mysql:host=" . DBHOST . ";dbname=" . DBNAME . ";charset=utf8";
        $con = new \PDO($dsn, DBUSER, DBPASS);
        return $con;
    }

    // Execute a query with optional parameters
    public function query($query, $data = []) {
        $con = $this->connect();
        $stm = $con->prepare($query);
        $check = $stm->execute($data);

        // For SELECT queries
        if (stripos($query, 'select') === 0 && $check) {
            $result = $stm->fetchAll(\PDO::FETCH_ASSOC);
            return $result ?: false;
        }

        // For INSERT, UPDATE, DELETE
        return $check;
    }
}
