<?php
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: https://interntestgui.drophere.xyz");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, X-Requested-With, Authorization");
    header("Access-Control-Allow-Credentials: true");
    header("HTTP/1.1 200 OK");
    exit();
}

//Check if the user is logged in
session_start();

$request_uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$resource = $request_uri[1] ?? '';

if($resource === 'users'){
    require_once './api/users.php';
    exit();
}


foreach (apache_request_headers() as $header => $value) {
    if($header === 'Authorization'){
        $token = $value;
    }
}
if(!isset($token)){
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized, token not found"]);
    exit();
}

if(!file_exists("token.txt")) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit();
}
$actual_token = file_get_contents("token.txt");
if($actual_token !== $token){
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit();
}

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
