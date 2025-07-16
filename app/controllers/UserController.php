<?php

require_once 'BaseController.php';

class UserController extends BaseController
{
    public function index()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
            header("Location: /dashboard");
            exit();
        }

        $users = $this->getUsers();

        require_once '../app/views/user_management.php';
    }

    public function getUsers()
    {
        $query = "SELECT * FROM users";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function updateUserRole()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
            header("Location: /dashboard");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
            $user_id = $_POST['user_id'];
            $role = $_POST['role'];

            $query = "UPDATE users SET role = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("si", $role, $user_id);
            $stmt->execute();
            $this->logActivity('User Role Updated', $user_id, 'user');
        }

        header("Location: /users");
    }

    public function deleteUser()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
            header("Location: /dashboard");
            exit();
        }

        if (isset($_GET['delete_user'])) {
            $user_id = $_GET['delete_user'];

            $query = "DELETE FROM users WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $this->logActivity('User Deleted', $user_id, 'user');
        }

        header("Location: /users");
    }
}
