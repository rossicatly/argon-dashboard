<?php

class Folder
{
    private $conn;
    private $table_name = "folders";

    public $id;
    public $user_id;
    public $name;
    public $parent_id;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " (user_id, name, parent_id) VALUES (?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));

        $stmt->bind_param("isi", $this->user_id, $this->name, $this->parent_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
