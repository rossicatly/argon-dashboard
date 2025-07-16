<?php

require_once 'BaseController.php';

class FileController extends BaseController
{
    public function upload()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
            $file = new File($this->conn);
            $file->user_id = $_SESSION['user_id'];
            $file->name = basename($_FILES['file']['name']);
            $file->type = $_FILES['file']['type'];
            $file->size = $_FILES['file']['size'];

            $target_dir = "../uploads/" . $_SESSION['user_id'] . "/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $target_file = $target_dir . $file->name;
            $file->path = $target_file;
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $allowed_types = array("pdf", "docx", "xlsx", "jpg", "png");

            if (!in_array($file_type, $allowed_types)) {
                echo "Sorry, only PDF, DOCX, XLSX, JPG, & PNG files are allowed.";
                return;
            }

            // Check if file already exists
            if (file_exists($target_file)) {
                echo "Sorry, file already exists.";
                return;
            }

            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                if ($file->create()) {
                    $file_id = $this->conn->insert_id;
                    $this->logActivity('File Uploaded', $file_id, 'file');
                    header("Location: /dashboard");
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }
}
