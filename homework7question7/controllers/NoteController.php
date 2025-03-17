<?php
class NoteController {
    public function handleRequest() {
        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if (strlen($title) < 4) {
                $error = 'Title must be at least 4 characters long.';
            } elseif (strlen($description) < 11) {
                $error = 'Description must be at least 11 characters long.';
            } else {
                // Sanitize input
                $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
                $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
                $success = 'Note successfully submitted!';
            }
        }
        require 'views/form.php';
    }
}
?>
