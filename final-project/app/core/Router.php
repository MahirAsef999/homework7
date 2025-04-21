<?php

namespace app\core;

use app\controllers\MainController;
use app\controllers\UserController;
use app\controllers\WorkoutController;
use app\controllers\AuthController;

class Router {
    public $uriArray;

    public function __construct() {
        $this->uriArray = $this->routeSplit();
        $this->handleMainRoutes();
        $this->handleUserRoutes();
        $this->handleWorkoutRoutes();
        $this->handleAuthRoutes();
    }

    // Split the route and remove query parameters
    protected function routeSplit() {
        $removeQueryParams = strtok($_SERVER["REQUEST_URI"], '?');
        return explode("/", $removeQueryParams);
    }

    // Main routes handling
    protected function handleMainRoutes() {
        if ($this->uriArray[1] === '' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $mainController = new MainController();
            $mainController->homepage();
        }
    }

    // User-related routes handling
    protected function handleUserRoutes() {
        if ($this->uriArray[1] === 'users' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $userController = new UserController();
            $userController->usersView();
        }

        if ($this->uriArray[1] === 'api' && $this->uriArray[2] === 'users' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $userController = new UserController();
            $userController->getUsers();
        }
    }

    // Workout-related routes handling
    protected function handleWorkoutRoutes() {
        $workoutController = new WorkoutController();

        // Handle search by exercise via GET /logs?exercise=...
        if ($this->uriArray[1] === 'logs' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET['exercise'])) {
                // Search by exercise (with the exercise query)
                $workoutController->searchLogsByExercise();
            } else {
                // Fetch all logs for the user
                $workoutController->getLogs();
            }
        }

        // Handle Update log via POST
        if ($this->uriArray[1] === 'logs' && $this->uriArray[2] === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $workoutController->updateLog();
        }

        // Handle Delete log via POST
        if ($this->uriArray[1] === 'logs' && $this->uriArray[2] === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $workoutController->deleteLog();
        }

        // Handle Create log via POST
        if ($this->uriArray[1] === 'logs' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $workoutController->postLog();
        }

        // View workout form page
        if ($this->uriArray[1] === 'workouts' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $workoutController->workoutView();
        }

        // Optional: direct form route
        if ($this->uriArray[1] === 'form' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $workoutController->workoutForm();
        }

        // ChatGPT advice route
        if ($this->uriArray[1] === 'advice' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $workoutController->getAdvice();
        }
    }

    // Authentication routes handling
    protected function handleAuthRoutes() {
        $auth = new AuthController();

        // Show login page
        if ($this->uriArray[1] === 'login' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $auth->showLogin();
        }

        // Show register page
        if ($this->uriArray[1] === 'register' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $auth->showRegister();
        }

        // Handle login via POST
        if ($this->uriArray[1] === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->login();
        }

        // Handle register via POST
        if ($this->uriArray[1] === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->register();
        }

        // Handle logout
        if ($this->uriArray[1] === 'logout' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            $auth->logout();
        }

        // Return logged-in user's username for personalization
        if ($this->uriArray[1] === 'userinfo' && $_SERVER['REQUEST_METHOD'] === 'GET') {
            session_start();
            header('Content-Type: application/json');
            echo json_encode([
                'username' => $_SESSION['username'] ?? null
            ]);
            exit;
        }
    }
}
