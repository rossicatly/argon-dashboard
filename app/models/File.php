<?php

class File
{
    private $conn;
    private $table_name = "files";

    public $id;
    public $user_id;
    public $folder_id;
    public $name;
    public $path;
    public $type;
    public $size;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " (user_id, name, path, type, size) VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->path = htmlspecialchars(strip_tags($this->path));
        $this->type = htmlspecialchars(strip_tags($this->type));

        $stmt->bind_param("isssi", $this->user_id, $this->name, $this->path, $this->type, $this->size);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
