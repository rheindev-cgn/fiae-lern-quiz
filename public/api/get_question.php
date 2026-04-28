<?php
header('Content-Type: application/json');

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    // __DIR__ ist /public/api. Wir müssen zwei Ebenen hoch: /public/api -> /public -> / (Root)
    // Dann in den /src Ordner.
    $base_dir = __DIR__ . '/../../src/'; 
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

require_once __DIR__ . '/../../src/Database.php';
// ... restlicher Code
use App\Repositories\QuestionRepository;

// POST Daten empfangen (die gewählten Kategorien aus app.js)
$data = json_decode(file_get_contents("php://input"), true);
$selectedCategories = $data['categories'] ?? [];

try {
    $database = new \Database();
    $db = $database->getConnection();
    $repo = new QuestionRepository($db);
    
    // IDs and Repository übergeben, damit die Methode weiß, welche Fragen sie ziehen soll
    $question = $repo->getRandomQuestionFromSelection($selectedCategories);

    if ($question) {
        echo json_encode($question);
    } else {
        echo json_encode(['error' => 'Keine Fragen für diese Auswahl gefunden']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}