<?php
session_start();

// Include the database configuration
require_once '../config/database.php';

// Include the models
require_once '../app/models/User.php';
require_once '../app/models/File.php';
require_once '../app/models/Folder.php';

// Include the controllers
require_once '../app/controllers/AuthController.php';
require_once '../app/controllers/FileController.php';
require_once '../app/controllers/DashboardController.php';

// Basic routing
$request = $_SERVER['REQUEST_URI'];
$conn = get_db_connection();

switch ($request) {
    case '/' :
        require __DIR__ . '/../app/views/home.php';
        break;
    case '/login' :
        $authController = new AuthController($conn);
        $authController->login();
        break;
    case '/register' :
        $authController = new AuthController($conn);
        $authController->register();
        break;
    case '/logout' :
        $authController = new AuthController($conn);
        $authController->logout();
        break;
    case '/dashboard' :
        $dashboardController = new DashboardController($conn);
        $dashboardController->index();
        break;
    case '/upload' :
        $fileController = new FileController($conn);
        $fileController->upload();
        break;
    case (preg_match('/\/share\?file_id=\d+/', $request) ? true : false) :
        require_once '../app/models/Share.php';
        require_once '../app/controllers/ShareController.php';
        $shareController = new ShareController($conn);
        $shareController->share();
        break;
    case (preg_match('/\/download\?token=.+/', $request) ? true : false) :
        require_once '../app/models/Share.php';
        require_once '../app/controllers/ShareController.php';
        $shareController = new ShareController($conn);
        $shareController->download();
        break;
    case '/users':
        require_once '../app/controllers/UserController.php';
        $userController = new UserController($conn);
        $userController->index();
        break;
    case '/users/update_role':
        require_once '../app/controllers/UserController.php';
        $userController = new UserController($conn);
        $userController->updateUserRole();
        break;
    case (preg_match('/\/users\/delete\?delete_user=\d+/', $request) ? true : false) :
        require_once '../app/controllers/UserController.php';
        $userController = new UserController($conn);
        $userController->deleteUser();
        break;
    default:
        http_response_code(404);
        require __DIR__ . '/../app/views/404.php';
        break;
}

$conn->close();
