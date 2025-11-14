<?php
class User {
    private $conn;
    private $table = 'users';

    public $id;
    public $name;      // pakai 'name' sesuai kolom tabel
    public $email;
    public $password;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all users
    public function read() {
        $stmt = $this->conn->prepare("SELECT id, name, email FROM " . $this->table);
        $stmt->execute();
        return $stmt;
    }

    // Create new user
    public function create() {
        $query = "INSERT INTO " . $this->table . " SET name=:name, email=:email, password=:password";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);

        // perbaikan Notice: gunakan variabel
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        $stmt->bindParam(":password", $hashedPassword);

        return $stmt->execute();
    }

    // Update user
    public function update() {
        $query = "UPDATE " . $this->table . " SET name=:name, email=:email, password=:password WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);

        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        $stmt->bindParam(":password", $hashedPassword);

        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    // Delete user
    public function delete() {
        $stmt = $this->conn->prepare("DELETE FROM " . $this->table . " WHERE id=:id");
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }
}
