<?php

class User
{
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $password;
    public $email;
    public $role;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " (username, password, email, role) VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->role = htmlspecialchars(strip_tags($this->role));

        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

        $stmt->bind_param("ssss", $this->username, $password_hash, $this->email, $this->role);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function findByUsername($username)
    {
        $query = "SELECT id, username, password, email, role FROM " . $this->table_name . " WHERE username = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();

        return $stmt->get_result();
    }
}
