<?php

class ActivityLog
{
    private $conn;
    private $table_name = "activity_logs";

    public $id;
    public $user_id;
    public $action;
    public $target_id;
    public $target_type;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " (user_id, action, target_id, target_type) VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("isis", $this->user_id, $this->action, $this->target_id, $this->target_type);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function getLogs($user_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
