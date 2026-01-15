<?php

class Database {
    private $conn;

    public function getConnection() {
        $this->conn = null;

        // Wir laden die Zugangsdaten relativ zum Speicherort dieser Datei
        $config = require __DIR__ . '/../config/db_creds.php';

        try {
            $this->conn = new PDO(
                "mysql:host=" . $config['host'] . ";dbname=" . $config['db_name'],
                $config['user'],
                $config['pass']
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            // Im Profi-Umfeld würden wir das in ein Log schreiben, statt es per echo auszugeben
            error_log("Verbindungsfehler: " . $exception->getMessage());
            return null;
        }

        return $this->conn;
    }
}