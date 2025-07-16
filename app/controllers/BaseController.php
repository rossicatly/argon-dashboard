<?php

require_once '../app/models/ActivityLog.php';

class BaseController
{
    protected $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    protected function logActivity($action, $target_id, $target_type)
    {
        $log = new ActivityLog($this->conn);
        $log->user_id = $_SESSION['user_id'];
        $log->action = $action;
        $log->target_id = $target_id;
        $log->target_type = $target_type;
        $log->create();
    }
}
