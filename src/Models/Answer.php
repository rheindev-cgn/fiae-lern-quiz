<?php
namespace App\Models;

class Answer {
    public $id;
    public $text;
    public $isCorrect;

    public function __construct($id, $text, $isCorrect) {
        $this->id = $id;
        $this->text = $text;
        $this->isCorrect = (bool)$isCorrect;
    }
}