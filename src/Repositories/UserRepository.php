<?php
namespace App\Repositories;

use App\Models\User;
use PDO;

class UserRepository {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findByUsername($username) {
        $sql = "SELECT id, username, password_hash, role_id FROM users WHERE username = :user";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user' => $username]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            // Wir geben das Array zurück, da wir das Passwort-Hash zum Vergleichen brauchen
            return $row;
        }
        return null;
    }
}