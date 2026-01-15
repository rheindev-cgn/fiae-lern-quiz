<?php

class Database {
    private $host = "127.0.0.1";
    private $db_name = "fiae_quiz";
    private $username = "root"; // Standard bei WAMPP
    private $password = "";     // Standard bei WAMPP oft leer
    public $conn;

    // Methode, um die Verbindung herzustellen
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            // Setze den Fehlermodus auf Exception, damit wir Fehler abfangen können
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Support für Umlaute (wichtig für deutsche Fragen!)
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Verbindungsfehler: " . $exception->getMessage();
        }

        return $this->conn;
    }
}