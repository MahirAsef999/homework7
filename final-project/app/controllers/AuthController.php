<?php

namespace app\controllers;
use app\models\User;

class AuthController extends Controller {

    public function showLogin() {
        $this->returnView('./assets/views/auth/login.html');
    }

    public function showRegister() {
        $this->returnView('./assets/views/auth/register.html');
    }

    public function login() {
        session_start();
        header('Content-Type: application/json');

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $userModel = new User();
        $user = $userModel->findByUsername($username);

        if (!$user) {
            echo json_encode(['success' => false, 'error' => 'Username does not exist']);
            return;
        }

        if (!password_verify($password, $user['password_hash'])) {
            echo json_encode(['success' => false, 'error' => 'Incorrect username or password']);
            return;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        echo json_encode(['success' => true]);
    }

    public function register() {
        header('Content-Type: application/json');
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (strlen($username) < 3 || strlen($password) < 4) {
            echo json_encode(['success' => false, 'error' => 'Username or password too short.']);
            return;
        }

        $userModel = new User();
        $existingUser = $userModel->findByUsername($username);

        if ($existingUser) {
            echo json_encode([
                'success' => false,
                'error' => "The username '$username' already exists"
            ]);
            return;
        }

        $userModel->create($username, $password);
        echo json_encode(['success' => true]);
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: /login?msg=" . urlencode("Logged out successfully.") . "&type=success");
        exit;
    }
}
