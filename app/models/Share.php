<?php

class Share
{
    private $conn;
    private $table_name = "shares";

    public $id;
    public $file_id;
    public $user_id;
    public $token;
    public $password;
    public $expires_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " (file_id, user_id, token, password, expires_at) VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        $this->token = bin2hex(random_bytes(16));

        $stmt->bind_param("iisss", $this->file_id, $this->user_id, $this->token, $this->password, $this->expires_at);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function findByToken($token)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE token = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $token);
        $stmt->execute();

        return $stmt->get_result();
    }
}
