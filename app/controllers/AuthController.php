<?php

require_once 'BaseController.php';

class AuthController extends BaseController
{
    private $user;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->user = new User($this->conn);
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            if (empty($username) || empty($email) || empty($password)) {
                echo "Please fill in all fields.";
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "Invalid email format.";
                return;
            }

            $this->user->username = $username;
            $this->user->email = $email;
            $this->user->password = $password;
            $this->user->role = 'Regular User';

            if ($this->user->create()) {
                $user_id = $this->conn->insert_id;
                $this->logActivity('User Registered', $user_id, 'user');
                header("Location: /login");
            } else {
                echo "Unable to register user.";
            }
        }

        require_once '../app/views/register.php';
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            if (empty($username) || empty($password)) {
                echo "Please fill in all fields.";
                return;
            }

            $stmt = $this->user->findByUsername($username);
            $user_data = $stmt->fetch_assoc();

            if ($user_data && password_verify($password, $user_data['password'])) {
                $_SESSION['user_id'] = $user_data['id'];
                $_SESSION['username'] = $user_data['username'];
                $_SESSION['role'] = $user_data['role'];
                $this->logActivity('User Logged In', $user_data['id'], 'user');
                header("Location: /dashboard");
            } else {
                echo "Invalid username or password.";
            }
        }

        require_once '../app/views/login.php';
    }

    public function logout()
    {
        $this->logActivity('User Logged Out', $_SESSION['user_id'], 'user');
        session_destroy();
        header("Location: /");
    }
}
