<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug-Modus aktiv</h1>";

// 1. Pfad-Check
$configFile = __DIR__ . '/config/db_creds.php';
if (file_exists($configFile)) {
    echo "✅ Die Config-Datei wurde gefunden unter: " . $configFile . "<br>";
} else {
    echo "❌ Die Config-Datei wurde NICHT gefunden! Erwarteter Pfad: " . $configFile . "<br>";
}

// 2. Datenbank-Verbindungstest (ohne Klasse, pur)
try {
    $config = require $configFile;
    $dsn = "mysql:host=" . $config['host'] . ";port=" . $config['port'] . ";dbname=" . $config['db_name'] . ";charset=" . $config['charset'];
    $test_db = new PDO($dsn, $config['user'], $config['pass']);
    echo "✅ PDO-Verbindung steht!<br>";
} catch (Exception $e) {
    echo "❌ PDO-Fehler: " . $e->getMessage() . "<br>";
}

echo "<p>Wenn du das hier siehst, funktioniert die Basis. Jetzt liegt der Fehler vermutlich im Autoloader oder in den Namespaces der Klassen.</p>";