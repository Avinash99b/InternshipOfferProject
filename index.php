<?php
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: https://interntestgui.drophere.xyz");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, X-Requested-With, Authorization");
    header("Access-Control-Allow-Credentials: true");
    header("HTTP/1.1 200 OK");
    exit();
}

$request_uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$resource = $request_uri[1] ?? '';

switch ($resource) {
    case 'employees':
        require_once './api/employees.php';
        break;
    case 'departments':
        require_once './api/departments.php';
        break;
    case 'positions':
        require_once './api/positions.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(["message" => "Endpoint not found"]);
}
