<?php
namespace App\Repositories;

use App\Models\Question;
use PDO;

class QuestionRepository {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Holt eine zufällige Frage basierend auf dem Status (Premium oder Gast)
    public function getRandomQuestion($isPremiumUser = false) {
        $sql = "SELECT * FROM questions";
        
        // Wenn kein Premium-Nutzer, zeige nur Nicht-Premium-Fragen
        if (!$isPremiumUser) {
            $sql .= " WHERE is_premium = 0";
        }
        
        $sql .= " ORDER BY RAND() LIMIT 1";

        $stmt = $this->db->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new Question($row['id'], $row['question_text'], $row['category_id'], $row['is_premium']);
    }
}