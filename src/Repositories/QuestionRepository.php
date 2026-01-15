<?php
namespace App\Repositories;

use App\Models\Question;
use App\Models\Answer; // Neu hinzugefügt
use PDO;

class QuestionRepository {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getRandomQuestion($isPremiumUser = false) {
        // 1. Frage holen
        $sql = "SELECT * FROM questions";
        if (!$isPremiumUser) {
            $sql .= " WHERE is_premium = 0";
        }
        $sql .= " ORDER BY RAND() LIMIT 1";

        $stmt = $this->db->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        $question = new Question($row['id'], $row['question_text'], $row['category_id'], $row['is_premium']);

        // 2. Passende Antworten laden (JOIN oder separate Abfrage)
        $this->loadAnswersForQuestion($question);

        return $question;
    }

    private function loadAnswersForQuestion(Question $question) {
        $sql = "SELECT * FROM answers WHERE question_id = :qid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['qid' => $question->id]);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $question->answers[] = new Answer($row['id'], $row['answer_text'], $row['is_correct']);
        }
    }
}