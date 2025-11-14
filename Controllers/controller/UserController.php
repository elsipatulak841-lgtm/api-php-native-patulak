<?php
header("Content-Type: application/json");

// Load database & model
require_once '../config/Database.php';
require_once '../models/User.php';

// Koneksi database
$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Ambil method dan ID dari URL
$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

switch($method) {

    case 'GET':
        if ($id) {
            $stmt = $db->prepare("SELECT id, name, email FROM users WHERE id=:id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($data);
        } else {
            $stmt = $user->read();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($users);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        // Validasi input
        if(!$data || !isset($data['name'], $data['email'], $data['password'])) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid input. name, email, password required."]);
            exit;
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $data['password'];

        if ($user->create()) {
            // Ambil user baru
            $lastId = $db->lastInsertId();
            $stmt = $db->prepare("SELECT id, name, email FROM users WHERE id=:id");
            $stmt->bindParam(':id', $lastId);
            $stmt->execute();
            $newUser = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($newUser);
        } else {
            echo json_encode(["message"=>"Failed to create user"]);
        }
        break; // <--- penting, harus ada break di akhir POST

    case 'PUT':
        if (!$id) {
            echo json_encode(["message" => "ID required for update"]);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if(!$data || !isset($data['name'], $data['email'], $data['password'])) {
            http_response_code(400);
            echo json_encode(["message"=>"Invalid input. name, email, password required."]);
            exit;
        }

        $user->id = $id;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $data['password'];

        if ($user->update()) {
            // Ambil user yang diupdate
            $stmt = $db->prepare("SELECT id, name, email FROM users WHERE id=:id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $updatedUser = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($updatedUser);
        } else {
            echo json_encode(["message"=>"Failed to update user"]);
        }
        break;

    case 'DELETE':
        if (!$id) {
            echo json_encode(["message" => "ID required for delete"]);
            exit;
        }

        $user->id = $id;

        if ($user->delete()) {
            echo json_encode(["message"=>"User deleted"]);
        } else {
            echo json_encode(["message"=>"Failed to delete user"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message"=>"Method not allowed"]);
        break;
}
