<?php
header('Content-Type: application/json');
session_start(); // Wichtig: Startet die Sitzung, um den Login zu speichern

// Autoloader (identisch zu get_question.php)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require $file;
});

require_once __DIR__ . '/../../src/Database.php';
use App\Repositories\UserRepository;

// Wir lesen die JSON-Daten aus dem Request-Body
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['username']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'message' => 'Daten unvollständig']);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $repo = new UserRepository($db);

    $userData = $repo->findByUsername($data['username']);

    if ($userData && password_verify($data['password'], $userData['password_hash'])) {
        // Login erfolgreich! Wir speichern die User-ID in der Session
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['role_id'] = $userData['role_id'];
        $_SESSION['username'] = $userData['username'];

        echo json_encode([
            'success' => true, 
            'username' => $userData['username'],
            'isPremium' => ($userData['role_id'] == 1) // Admin ist hier auch Premium
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ungültige Anmeldedaten']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}