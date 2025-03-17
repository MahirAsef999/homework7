<?php
class Note {
    private $title;
    private $description;

    public function __construct($title, $description) {
        $this->title = trim($title);
        $this->description = trim($description);
    }

    public function validate() {
        if (strlen($this->title) < 4) {
            return "Title must be at least 4 characters long.";
        }
        if (strlen($this->description) < 11) {
            return "Description must be at least 11 characters long.";
        }
        return null; // No errors
    }

    public function sanitize() {
        $this->title = htmlspecialchars($this->title, ENT_QUOTES, 'UTF-8');
        $this->description = htmlspecialchars($this->description, ENT_QUOTES, 'UTF-8');
    }

    public function getTitle() {
        return $this->title;
    }

    public function getDescription() {
        return $this->description;
    }
}
?>
