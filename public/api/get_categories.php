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

try {
    $database = new \Database(); // Backslash falls Database nicht im Namespace
    $db = $database->getConnection();
    
    // Einfache Abfrage der Kategorien
    $stmt = $db->query("SELECT id, short_name, full_name FROM categories ORDER BY id ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($categories);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}