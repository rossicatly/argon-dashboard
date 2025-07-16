<?php

require_once 'BaseController.php';

class DashboardController extends BaseController
{
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_folder'])) {
            $this->createFolder();
        }

        if (isset($_GET['delete_file'])) {
            $this->deleteFile($_GET['delete_file']);
        }

        if (isset($_GET['delete_folder'])) {
            $this->deleteFolder($_GET['delete_folder']);
        }

        $folder_id = isset($_GET['folder_id']) ? $_GET['folder_id'] : null;
        $search = isset($_GET['search']) ? $_GET['search'] : null;
        $type = isset($_GET['type']) ? $_GET['type'] : null;

        $files = $this->getFiles($folder_id, $search, $type);
        $folders = $this->getFolders($folder_id);
        $logs = $this->getLogs();

        require_once '../app/views/dashboard.php';
    }

    public function createFolder()
    {
        $folder = new Folder($this->conn);
        $folder->user_id = $_SESSION['user_id'];
        $folder->name = $_POST['folder_name'];
        $folder->parent_id = null; // For now, all folders are top-level

        if ($folder->create()) {
            $folder_id = $this->conn->insert_id;
            $this->logActivity('Folder Created', $folder_id, 'folder');
            header("Location: /dashboard");
        } else {
            echo "Error creating folder.";
        }
    }

    public function getFiles($folder_id = null, $search = null, $type = null)
    {
        $query = "SELECT * FROM files WHERE user_id = ?";
        $params = [$_SESSION['user_id']];
        $types = "i";

        if ($folder_id) {
            $query .= " AND folder_id = ?";
            $params[] = $folder_id;
            $types .= "i";
        } else {
            $query .= " AND folder_id IS NULL";
        }

        if ($search) {
            $query .= " AND name LIKE ?";
            $params[] = "%" . $search . "%";
            $types .= "s";
        }

        if ($type) {
            $query .= " AND type LIKE ?";
            $params[] = "%" . $type . "%";
            $types .= "s";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getFolders($folder_id = null)
    {
        if ($folder_id) {
            $query = "SELECT * FROM folders WHERE user_id = ? AND parent_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ii", $_SESSION['user_id'], $folder_id);
        } else {
            $query = "SELECT * FROM folders WHERE user_id = ? AND parent_id IS NULL";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $_SESSION['user_id']);
        }
        $stmt->execute();
        return $stmt->get_result();
    }

    public function deleteFile($id)
    {
        $query = "SELECT path FROM files WHERE id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $id, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            unlink($result['path']);
            $query = "DELETE FROM files WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $this->logActivity('File Deleted', $id, 'file');
        }

        header("Location: /dashboard");
    }

    public function deleteFolder($id)
    {
        $query = "DELETE FROM folders WHERE id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $id, $_SESSION['user_id']);
        $stmt->execute();
        $this->logActivity('Folder Deleted', $id, 'folder');

        header("Location: /dashboard");
    }

    public function getLogs()
    {
        $log = new ActivityLog($this->conn);
        return $log->getLogs($_SESSION['user_id']);
    }
}
