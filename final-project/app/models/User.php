<?php

namespace app\models;

class User extends Model {
    protected $table = 'users';

    public function findByUsername($username) {
        $result = $this->query("SELECT * FROM users WHERE username = ?", [$username]);
        return $result ? $result[0] : null;
    }

    public function create($username, $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        return $this->query("INSERT INTO users (username, password_hash) VALUES (?, ?)", [$username, $hash]);
    }
}
