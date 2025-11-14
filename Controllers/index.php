<?php
echo json_encode([
    "message" => "CRUD PDO API ready",
    "endpoints" => [
        "GET" => "/controller/UserController.php",
        "POST" => "/controller/UserController.php",
        "PUT" => "/controller/UserController.php?id={id}",
        "DELETE" => "/controller/UserController.php?id={id}"
    ]
]);
