<?php
require_once __DIR__ . '/src/Database.php';

$database = new Database();
$db = $database->getConnection();

// Passwörter sicher hashen
$passwordAdmin = password_hash('admin123', PASSWORD_DEFAULT);
$passwordUser = password_hash('test123', PASSWORD_DEFAULT);

try {
    // 1. Rollen anlegen (falls noch nicht geschehen)
    $db->exec("INSERT IGNORE INTO roles (id, role_name) VALUES (1, 'admin'), (2, 'user')");

    // 2. Nutzer anlegen
    $sql = "INSERT INTO users (username, password_hash, role_id) VALUES (:user, :hash, :role)";
    $stmt = $db->prepare($sql);

    // Admin
    $stmt->execute(['user' => 'admin', 'hash' => $passwordAdmin, 'role' => 1]);
    // Test-User
    $stmt->execute(['user' => 'testuser', 'hash' => $passwordUser, 'role' => 2]);

    echo "✅ Test-Nutzer wurden erfolgreich angelegt!";
} catch (Exception $e) {
    echo "❌ Fehler: " . $e->getMessage();
}