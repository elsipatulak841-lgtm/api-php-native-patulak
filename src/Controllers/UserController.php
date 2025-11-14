<?php

class UserController
{
    public function index()
    {
        echo json_encode([
            "success" => true,
            "data" => [
                ["id" => 1, "name" => "Admin"],
                ["id" => 2, "name" => "Novi"]
            ]
        ]);
    }

    public function show($id)
    {
        echo json_encode([
            "success" => true,
            "data" => ["id" => $id, "name" => "User " . $id]
        ]);
    }
}
