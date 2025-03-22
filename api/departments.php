<?php

require_once __DIR__.'/../config/database.php';

//header('Content-Type: application/json');

$database = new Database();
$conn = $database->getConnection();

// Handle GET request to fetch all departments
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT department_name as name,id FROM departments";
    $result = $conn->query($sql);

    if ($result->rowCount() > 0) {
        $departments = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $departments[] = $row;
        }
        echo json_encode($departments);
    } else {
        echo json_encode([]);
    }
}

// Handle POST request to add a department
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $department_name = $data['department_name'] ?? '';

    if (!empty($department_name)) {
        $stmt = $conn->prepare("INSERT INTO departments (department_name) VALUES (?)");

        if ($stmt->execute([$department_name])) {
            echo json_encode(["message" => "Department added successfully"]);
        } else {
            echo json_encode(["error" => "Failed to add department"]);
        }
        $stmt->closeCursor();
    } else {
        echo json_encode(["error" => "Invalid department name"]);
    }
}



// Handle POST request to add a department
elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $request_uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    $departmentId = $request_uri[sizeof($request_uri)-1];

    if(intval($departmentId)===0){
        $id = null;
    }else{
        $id=intval($departmentId);
    }

    if (!empty($departmentId)) {
        $stmt = $conn->prepare("Delete from departments where id = ?");

        if ($stmt->execute([$id])) {
            echo json_encode(["message" => "Department added successfully"]);
        } else {
            echo json_encode(["error" => "Failed to add department"]);
        }
        $stmt->closeCursor();
    } else {
        echo json_encode(["error" => "Invalid department Id"]);
    }
}
