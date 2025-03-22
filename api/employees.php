<?php

require_once __DIR__ . '/../config/database.php';

$database = new Database();
$conn = $database->getConnection();
$method = $_SERVER['REQUEST_METHOD'];
$request_uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

$empId = $request_uri[sizeof($request_uri) - 1];

if (intval($empId) === 0) {
    $id = null;
} else {
    $id = intval($empId);
}


// Retrieve all employees with department and position names
if ($method === 'GET' && !$id) {
    $stmt = $conn->prepare("
        SELECT employees.id, employees.name, departments.department_name, positions.position_name
        FROM employees
        JOIN departments ON employees.department_id = departments.id
        JOIN positions ON employees.position_id = positions.id
    ");
    $stmt->execute();
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($employees);
    exit;
}

// Retrieve a single employee by ID
if ($method === 'GET' && $id) {
    $stmt = $conn->prepare("
        SELECT employees.id, employees.name, departments.department_name, positions.position_name
        FROM employees
        JOIN departments ON employees.department_id = departments.id
        JOIN positions ON employees.position_id = positions.id
        WHERE employees.id = ?
    ");
    $stmt->execute([$id]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($employee) {
        echo json_encode($employee);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Employee not found"]);
    }
    exit;
}

// Add a new employee
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['name'], $data['department_id'], $data['position_id'])) {
        http_response_code(400);
        echo json_encode(["message" => "Missing required fields"]);
        exit;
    }

    if (empty($data['name']) || empty($data['department_id']) || empty($data['position_id'])) {
        http_response_code(400);
        echo json_encode(["message" => "Missing required fields"]);
        exit;
    }

    //check if name is greater than 3 characters
    if (strlen($data['name']) < 3) {
        http_response_code(400);
        echo json_encode(["message" => "Name should be greater than 3 characters"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO employees (name, department_id, position_id) VALUES (?, ?, ?)");
    if ($stmt->execute([$data['name'], $data['department_id'], $data['position_id']])) {

        echo json_encode(["message" => "Employee added successfully"]);
    } else {
        http_response_code(500);
        echo json_encode([ "message" => "Failed to add employee"]);
    }
    exit;
}

// Update an existing employee
if ($method === 'PUT' && $id) {
    $data = json_decode(file_get_contents("php://input"), true);

    $stmt = $conn->prepare("UPDATE employees SET name = ?, department_id = ?, position_id = ? WHERE id = ?");
    if(!$stmt->execute([$data['name'], $data['department_id'], $data['position_id'], $id])){
        http_response_code(500);
        echo json_encode(["message" => "Failed to update employee"]);
        exit;
    }

    echo json_encode(["message" => "Employee updated successfully"]);
    exit;
}

// Delete an employee
if ($method === 'DELETE' && $id) {
    $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(["message" => "Employee deleted successfully"]);
    exit;
}

// Invalid request
http_response_code(405);
echo json_encode(["message" => "Method not allowed"]);
