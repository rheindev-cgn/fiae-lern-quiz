<?php
/**
 * API Endpunkt: Liefert eine zufällige Frage als JSON
 */

header('Content-Type: application/json'); // Sagt dem Browser: "Hier kommt JSON"

// Autoloader einbinden (relativ zum api-Ordner)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../../src/'; // Zwei Ebenen hoch zu /src/
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

require_once __DIR__ . '/../../src/Database.php';

use App\Repositories\QuestionRepository;

try {
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        throw new Exception("Datenbankverbindung fehlgeschlagen");
    }

    $repo = new QuestionRepository($db);
    
    // Später prüfen wir hier, ob der User eingeloggt ist.
    // Für den Start simulieren wir einen Gast-User (false)
    $question = $repo->getRandomQuestion(false);

    if ($question) {
        // Wir geben das gesamte Question-Objekt (inkl. Antworten) als JSON aus
        echo json_encode($question);
    } else {
        echo json_encode(['error' => 'Keine Fragen gefunden']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}