<?php
namespace App\Models;

class Question {
    public $id;
    public $text;
    public $categoryId;
    public $isPremium;
    public $answers = [];

    public function __construct($id, $text, $categoryId, $isPremium) {
        $this->id = $id;
        $this->text = $text;
        $this->categoryId = $categoryId;
        $this->isPremium = $isPremium;
    }
}