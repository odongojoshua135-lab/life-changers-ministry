<?php
// admin/events.php
session_start();

// 1. AUTHENTICATION GUARD
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';

$message = '';
$error = '';
$upload_dir = __DIR__ . '/../uploads/events/';

if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// 2. HANDLE DELETE ACTION
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT banner_path FROM events WHERE id = ?");
        $stmt->execute([$delete_id]);
        $eventToDelete = $stmt->fetch();

        if ($eventToDelete && !empty($eventToDelete['banner_path'])) {
            $filePath = __DIR__ . '/../' . ltrim($eventToDelete['banner_path'], '/');
            if (file_exists($filePath) && is_file($filePath)) {
                unlink($filePath);
            }
        }

        $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$delete_id]);
        header("Location: events.php?msg=deleted");
        exit;
    } catch (PDOException $e) {
        $error = "Error deleting event: " . $e->getMessage();
    }
}

// 3. HANDLE CREATE / EDIT SUBMISSION
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id    = !empty($_POST['event_id']) ? (int)$_POST['event_id'] : null;
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date  = trim($_POST['event_date'] ?? '');
    $event_time  = trim($_POST['event_time'] ?? '');
    $location    = trim($_POST['location'] ?? 'Main Sanctuary');

    if (empty($title) || empty($event_date)) {
        $error = "Please fill in all required fields (Title and Date).";
    } else {
        $banner_path = null;

        if ($event_id) {
            $stmt = $pdo->prepare("SELECT banner_path FROM events WHERE id = ?");
            $stmt->execute([$event_id]);
            $existing = $stmt->fetch();
            $banner_path = $existing['banner_path'] ?? null;
        }

        if (isset($_FILES['banner']) && $_FILES['banner']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['banner']['tmp_name'];
            $fileName    = $_FILES['banner']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $fileTmpPath);
            finfo_close($finfo);

            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

            if (in_array($fileExtension, $allowedExtensions) && in_array($mimeType, $allowedMimeTypes)) {
                $newFileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExtension;
                $destPath    = $upload_dir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    // Remove old banner if replacing
                    if ($event_id && !empty($existing['banner_path'])) {
                        $oldFilePath = __DIR__ . '/../' . ltrim($existing['banner_path'], '/');
                        if (file_exists($oldFilePath) && is_file($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }
                    $banner_path = 'uploads/events/' . $newFileName;
                } else {
                    $error = "Failed to move uploaded flyer to the target directory.";
                }
            } else {
                $error = "Invalid file type. Allowed formats: JPG, JPEG, PNG, WEBP, GIF.";
            }
        }

        if (empty($error)) {
            try {
                if ($event_id) {
                    $stmt = $pdo->prepare("UPDATE events SET title = ?, description = ?, event_date = ?, event_time = ?, location = ?, banner_path = ? WHERE id = ?");
                    $stmt->execute([$title, $description, $event_date, $event_time, $location, $banner_path, $event_id]);
                    $message = "Event updated successfully!";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, event_time, location, banner_path, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                    $stmt->execute([$title, $description, $event_date, $event_time, $location, $banner_path]);
                    $message = "Event created successfully!";
                }
            } catch (PDOException $e) {
                $error = "Database operation failed: " . $e->getMessage();
            }
        }
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $message = "Event deleted successfully!";
}

$editEvent = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $edit_id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$edit_id]);
    $editEvent = $stmt->fetch();
}

$stmt = $pdo->query("SELECT * FROM events ORDER BY event_date DESC");
$allEvents = $stmt->fetchAll();
?>