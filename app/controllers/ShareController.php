<?php

require_once 'BaseController.php';
require_once '../app/models/Share.php';

class ShareController extends BaseController
{
    public function share()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['file_id'])) {
            $share = new Share($this->conn);
            $share->file_id = $_GET['file_id'];
            $share->user_id = $_SESSION['user_id'];
            $share->password = $_POST['password'];
            $share->expires_at = $_POST['expires_at'];

            if ($share->create()) {
                $share_id = $this->conn->insert_id;
                $this->logActivity('File Shared', $share->file_id, 'file');
                $share_link = "http://" . $_SERVER['HTTP_HOST'] . "/download?token=" . $share->token;
                echo "Share link: <a href='" . $share_link . "'>" . $share_link . "</a>";
            } else {
                echo "Error creating share link.";
            }
        } else {
            require_once '../app/views/share.php';
        }
    }

    public function download()
    {
        if (isset($_GET['token'])) {
            $share = new Share($this->conn);
            $share_data = $share->findByToken($_GET['token'])->fetch_assoc();

            if ($share_data) {
                if ($share_data['expires_at'] && strtotime($share_data['expires_at']) < time()) {
                    echo "Share link has expired.";
                    return;
                }

                if ($share_data['password']) {
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
                        if ($_POST['password'] === $share_data['password']) {
                            $this->logActivity('File Downloaded', $share_data['file_id'], 'file');
                            $this->downloadFile($share_data['file_id']);
                        } else {
                            echo "Invalid password.";
                        }
                    }
                    require_once '../app/views/share_password.php';
                } else {
                    $this->logActivity('File Downloaded', $share_data['file_id'], 'file');
                    $this->downloadFile($share_data['file_id']);
                }
            } else {
                echo "Invalid share link.";
            }
        }
    }

    private function downloadFile($file_id)
    {
        $file = new File($this->conn);
        $query = "SELECT * FROM files WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $file_id);
        $stmt->execute();
        $file_data = $stmt->get_result()->fetch_assoc();

        if ($file_data) {
            $file_path = '../' . $file_data['path'];
            if (file_exists($file_path)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file_path));
                readfile($file_path);
                exit;
            }
        }
    }
}
