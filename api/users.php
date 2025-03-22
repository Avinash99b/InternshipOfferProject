<?php

require_once __DIR__.'/../config/database.php';

//header('Content-Type: application/json');


//Employee id =1, password = Admin123
//Write simple authentication and store the employee id in session after logging in

header("Access-Control-Allow-Origin: https://interntestgui.drophere.xyz");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, X-Requested-With, Authorization");
    header("Access-Control-Allow-Credentials: true");
    header("HTTP/1.1 200 OK");
    exit();
}

$resource = explode('/', trim($_SERVER['REQUEST_URI'], '/'))[2] ?? '';
if(empty($resource)){
    http_response_code(404);
    echo json_encode(["message" => "Endpoint not found"]);
    exit();
}

function writeTokenToFile(){
    $token = bin2hex(random_bytes(16));
    $file = fopen("token.txt", "w") or die("Unable to open file!");
    $result = fwrite($file, $token);
    if(!$result){
        http_response_code(500);
        echo json_encode(["message" => "Failed to write token"]);
        exit();
    }
    fclose($file);
    return $token;
}
if($resource === 'login'){
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        if ($data['employee_id'] == 1 && $data['password'] == 'Admin123') {
            http_response_code(200);
            $token = writeTokenToFile();
            echo json_encode(["message" => "Login successful","token"=>$token]);
        } else {
            http_response_code(401);
            echo json_encode(["message" => "Invalid credentials"]);
        }
    } else {
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
    }
}else{
    http_response_code(404);
    echo json_encode(["message" => "Endpoint not found"]);
}
