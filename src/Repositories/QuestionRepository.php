<?php
namespace App\Repositories;

use PDO;

class QuestionRepository {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getRandomQuestionFromSelection($categoryIds) {
        // Falls nichts gewählt wurde, nehmen wir alle (Sicherheit)
        $whereClause = "";
        if (!empty($categoryIds)) {
            $ids = implode(',', array_map('intval', $categoryIds));
            $whereClause = "WHERE q.category_id IN ($ids)";
        }

        // 1. Eine zufällige Frage holen
        $query = "SELECT q.id, q.question_text, q.explanation, c.full_name as category_name 
                  FROM questions q 
                  JOIN categories c ON q.category_id = c.id 
                  $whereClause 
                  ORDER BY RAND() LIMIT 1";
        
        $stmt = $this->db->query($query);
        $question = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$question) return null;

        // 2. Die 4 Antworten dazu holen
        $stmtAnswers = $this->db->prepare("SELECT answer_text as text, is_correct FROM answers WHERE question_id = ?");
        $stmtAnswers->execute([$question['id']]);
        $answers = $stmtAnswers->fetchAll(PDO::FETCH_ASSOC);

        // Format für das Frontend (app.js) aufbereiten
        return [
            'id' => $question['id'],
            'text' => $question['question_text'],
            'explanation' => $question['explanation'],
            'category_name' => $question['category_name'],
            'answers' => $answers
        ];
    }
}