<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../Src/Router.php";
require_once __DIR__ . "/../Src/Controllers/UserController.php";

$router = new Router();
$userController = new UserController();

// ROUTE TANPA NAMA FOLDER
$router->add("GET", "api/v1/users", [$userController, "index"]);
$router->add("GET", "api/v1/users/{id}", [$userController, "show"]);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_GET['url'] ?? '');
