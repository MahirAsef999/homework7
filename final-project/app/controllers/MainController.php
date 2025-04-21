<?php

namespace app\controllers;

class MainController extends Controller {
    public function homepage() {
        session_start();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['username' => $_SESSION['username'] ?? null]);
            return;
        }

        $this->returnView('./assets/views/main/homepage.html');
    }
}
