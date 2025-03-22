<?php

require_once __DIR__ . '/../config/database.php';

//header('Content-Type: application/json');

$database = new Database();
$conn = $database->getConnection();

// Handle GET request to fetch all positions
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT position_name as name,id FROM positions";
    $result = $conn->query($sql);

    if ($result->rowCount() > 0) {
        $positions = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $positions[] = $row;
        }
        echo json_encode($positions);
    } else {
        echo json_encode([]);
    }
} // Handle POST request to add a position
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $position_name = $data['position_name'] ?? '';

    if (!empty($position_name)) {
        $stmt = $conn->prepare("INSERT INTO positions (position_name) VALUES (?)");

        if ($stmt->execute($position_name)) {
            echo json_encode(["message" => "Position added successfully"]);
        } else {
            echo json_encode(["error" => "Failed to add position"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Invalid position name"]);
    }
}
