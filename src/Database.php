<?php

class Database {
    private $conn;

    public function getConnection() {
        $this->conn = null;
        $config = require __DIR__ . '/../config/db_creds.php';

        try {
            // DSN mit Port und Charset erweitern
            $dsn = "mysql:host=" . $config['host'] . 
                   ";port=" . $config['port'] . 
                   ";dbname=" . $config['db_name'] . 
                   ";charset=" . $config['charset'];
            
            $this->conn = new PDO($dsn, $config['user'], $config['pass']);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            error_log("Verbindungsfehler: " . $exception->getMessage());
            echo "Verbindungsfehler: " . $exception->getMessage();
            return null;
        }

        return $this->conn;
    }
}