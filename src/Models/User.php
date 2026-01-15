<?php
namespace App\Models;

class User {
    public $id;
    public $username;
    public $roleId;

    public function __construct($id, $username, $roleId) {
        $this->id = $id;
        $this->username = $username;
        $this->roleId = $roleId;
    }
}