<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Autoloader für Namespaces (App\...)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

// 2. Database-Klasse laden (hat keinen Namespace)
require_once __DIR__ . '/src/Database.php';

use App\Repositories\QuestionRepository;

echo "<h1>Quiz-Projekt: System-Check</h1>";

try {
    $database = new Database();
    $db = $database->getConnection();

    if ($db) {
        echo "✅ Datenbank-Verbindung erfolgreich.<br>";

        $repo = new QuestionRepository($db);
        // Test: Eine Zufallsfrage für Gäste (Premium = 0)
        $question = $repo->getRandomQuestion(false);

        if ($question) {
            echo "✅ Repository-Test erfolgreich. Frage geladen:<br>";
            echo "<pre style='background: #eee; padding: 10px;'>";
            print_r($question);
            echo "</pre>";
        } else {
            echo "⚠️ Verbindung steht, aber keine passenden Fragen gefunden. Hast du das SQL-Seed ausgeführt?";
        }
    }
} catch (Exception $e) {
    echo "❌ Kritischer Fehler: " . $e->getMessage();
}