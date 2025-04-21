<?php
header('Content-Type: application/json');

// Fetch random quote from Quotable API
$response = file_get_contents("https://api.quotable.io/random?tags=fitness|inspirational");

if ($response !== false) {
    $data = json_decode($response, true);
    echo json_encode([
        "quote" => $data['content'] . " — " . $data['author']
    ]);
} else {
    echo json_encode([
        "quote" => "Stay strong. Stay consistent!"
    ]);
}
