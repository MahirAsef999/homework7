<?php

namespace app\controllers;

use app\models\Workout;
use Orhanerday\OpenAi\OpenAi;

class WorkoutController extends Controller
{
    public function workoutForm()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }
        $this->returnView('./assets/views/main/homepage.html');
    }

    public function workoutView()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }
        $this->returnView('./assets/views/users/workoutView.html');
    }

    public function getLogs()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            $this->returnJSON(['error' => 'Unauthorized']);
            return;
        }

        $model = new Workout();

        // Handle exercise search
        if (isset($_GET['exercise']) && trim($_GET['exercise']) !== '') {
            $exercise = trim($_GET['exercise']);
            $logs = $model->getLogsByExercise($_SESSION['user_id'], $exercise);
        } else {
            $logs = $model->getLogsByUser($_SESSION['user_id']);
        }

        $this->returnJSON($logs);
    }

    public function postLog()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            $this->returnJSON(['error' => 'Unauthorized']);
            return;
        }

        $data = [
            'date'     => $_POST['date'] ?? '',
            'exercise' => $_POST['exercise'] ?? '',
            'weight'   => $_POST['weight'] ?? '',
            'sets'     => $_POST['sets'] ?? 'N/A',
            'reps'     => $_POST['reps'] ?? 'N/A',
            'time'     => $_POST['time'] ?? '',
            'distance' => $_POST['distance'] ?? '',
            'calories' => $_POST['calories'] ?? '',
            'user_id'  => $_SESSION['user_id']
        ];

        if (empty($data['date']) || empty($data['exercise'])) {
            http_response_code(400);
            $this->returnJSON(['error' => 'Invalid input']);
            return;
        }

        $model = new Workout();
        $model->saveLog($data);
        $this->returnJSON(['success' => true]);
    }

    public function updateLog()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            $this->returnJSON(['error' => 'Unauthorized']);
            return;
        }

        $data = [
            'id'       => $_POST['id'] ?? '',
            'date'     => $_POST['date'] ?? '',
            'exercise' => $_POST['exercise'] ?? '',
            'weight'   => $_POST['weight'] ?? '',
            'sets'     => $_POST['sets'] ?? 'N/A',
            'reps'     => $_POST['reps'] ?? 'N/A',
            'time'     => $_POST['time'] ?? '',
            'distance' => $_POST['distance'] ?? '',
            'calories' => $_POST['calories'] ?? '',
            'user_id'  => $_SESSION['user_id']
        ];

        if (!$data['id'] || !is_numeric($data['id'])) {
            http_response_code(400);
            $this->returnJSON(['error' => 'Invalid input']);
            return;
        }

        $model = new Workout();
        $model->updateLog($data);
        $this->returnJSON(['success' => true]);
    }

    public function deleteLog()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            $this->returnJSON(['error' => 'Unauthorized']);
            return;
        }

        $id = $_POST['id'] ?? null;
        if (!$id || !is_numeric($id)) {
            http_response_code(400);
            $this->returnJSON(['error' => 'Invalid input']);
            return;
        }

        $model = new Workout();
        $model->deleteLog($id, $_SESSION['user_id']);
        $this->returnJSON(['success' => true]);
    }

    public function getAdvice()
    {
        if (!isset($_GET['question']) || trim($_GET['question']) === '') {
            $this->returnJSON(['error' => 'No question provided']);
            return;
        }

        $question = $_GET['question'];
        $open_ai = new OpenAi($_ENV['OPENAI_API_KEY']);

        $response = $open_ai->completion([
            'model' => 'gpt-3.5-turbo-instruct',
            'prompt' => "Give me workout or fitness advice for: " . $question,
            'temperature' => 0.7,
            'max_tokens' => 100,
        ]);

        $response = json_decode($response, true);
        $advice = trim($response["choices"][0]["text"] ?? 'No advice generated.');

        $this->returnJSON(['advice' => $advice]);
    }
}
