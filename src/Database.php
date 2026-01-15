<?php
// Falls du Namespaces nutzt, hier: namespace App;

class Database {
    private $pdo;

    public function getConnection() {
        if ($this->pdo === null) {
            try {
                // Pfad von /src aus eine Ebene hoch zu / und dann in /config
                $config = require __DIR__ . '/../config/db_creds.php';

                $dsn = "mysql:host=" . $config['host'] . 
                       ";port=" . $config['port'] . 
                       ";dbname=" . $config['database'] . 
                       ";charset=" . $config['charset'];

                $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);

            } catch (Exception $e) {
                throw new Exception("DB-Verbindung fehlgeschlagen: " . $e->getMessage());
            }
        }
        return $this->pdo;
    }
}