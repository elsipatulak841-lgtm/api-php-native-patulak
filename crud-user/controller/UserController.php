<?php
header("Content-Type: application/json");

require_once "../config/Database.php";
require_once "../models/User.php";

// Koneksi database
$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

switch($method) {
    case "GET":
        if ($id) {
            $stmt = $db->prepare("SELECT id, name, email FROM users WHERE id=:id");
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            $stmt = $user->read();
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);

        // Validasi input
        if (!$data || !isset($data['name'], $data['email'], $data['password'])) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid input. name, email, password required."]);
            exit;
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $data['password'];

        if ($user->create()) {
            // Ambil ID user baru
            $newUserId = $db->lastInsertId();
            $stmt = $db->prepare("SELECT id, name, email FROM users WHERE id=:id");
            $stmt->bindParam(':id', $newUserId);
            $stmt->execute();
            $newUser = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode([
                "message" => "User created",
                "user" => $newUser
            ]);
        } else {
            echo json_encode(["message" => "Failed to create user"]);
        }
        break;

    case "PUT":
        if (!$id) {
            echo json_encode(["message" => "ID required"]);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data || !isset($data['name'], $data['email'], $data['password'])) {
            echo json_encode(["message" => "Invalid input"]);
            exit;
        }

        $user->id = $id;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $data['password'];

        if ($user->update()) {
            echo json_encode(["message" => "User updated"]);
        } else {
            echo json_encode(["message" => "Failed to update user"]);
        }
        break;

    case "DELETE":
        if (!$id) {
            echo json_encode(["message" => "ID required"]);
            exit;
        }

        $user->id = $id;

        if ($user->delete()) {
            echo json_encode(["message" => "User deleted"]);
        } else {
            echo json_encode(["message" => "Failed to delete user"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
