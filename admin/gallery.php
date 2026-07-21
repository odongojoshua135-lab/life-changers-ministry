<?php
// admin/gallery.php
session_start();
require_once '../config/db.php';

// Authentication Guard
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$success = '';
$error = '';

// --- HANDLE IMAGE UPLOAD (CREATE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_image'])) {
    $title = trim($_POST['title']);
    $category = $_POST['category'];
    
    // Check if file was uploaded without errors
    if (isset($_FILES['gallery_file']) && $_FILES['gallery_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['gallery_file']['tmp_name'];
        $fileName = $_FILES['gallery_file']['name'];
        $fileSize = $_FILES['gallery_file']['size'];
        
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        // Allowed file types
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($fileExtension, $allowedExtensions)) {
            // Max file size: 5MB (5 * 1024 * 1024 bytes)
            if ($fileSize <= 5242880) {
                // Ensure the dynamic upload destination folder exists
                $uploadDir = '../uploads/gallery/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // Create a completely unique filename to prevent overwriting
                $newFileName = time() . '_' . md5(uniqid()) . '.' . $fileExtension;
                $dest_path = $uploadDir . $newFileName;
                
                // Move file from temporary directory to target folder
                if(move_uploaded_file($fileTmpPath, $dest_path)) {
                    // Save relative path path into the database for frontend usage
                    $db_path = 'uploads/gallery/' . $newFileName;
                    
                    $stmt = $pdo->prepare("INSERT INTO gallery (title, image_path, category) VALUES (:title, :image_path, :category)");
                    $stmt->execute([
                        'title' => $title,
                        'image_path' => $db_path,
                        'category' => $category
                    ]);
                    $success = 'Image uploaded and categorized successfully!';
                } else {
                    $error = 'There was an issue moving the file to the upload directory.';
                }
            } else {
                $error = 'File size is too large. Maximum limit is 5MB.';
            }
        } else {
            $error = 'Invalid file extension type. Allowed: JPG, JPEG, PNG, WEBP.';
        }
    } else {
        $error = 'Please select a valid image file to upload.';
    }
}

// --- HANDLE IMAGE DELETION (DELETE) ---
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Fetch image record to delete the file from physical disk space
    $stmt = $pdo->prepare("SELECT image_path FROM gallery WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $image = $stmt->fetch();
    
    if ($image) {
        $physicalPath = '../' . $image['image_path'];
        // Remove file from folder if it exists
        if (file_exists($physicalPath)) {
            unlink($physicalPath);
        }
        
        // Remove reference row from database table
        $deleteStmt = $pdo->prepare("DELETE FROM gallery WHERE id = :id");
        $deleteStmt->execute(['id' => $id]);
        $success = 'Image deleted successfully.';
    } else {
        $error = 'Image record not found.';
    }
}

// Fetch all images grouped by latest addition
$images = $pdo->query("SELECT * FROM gallery ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery - Life Changers Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-900 text-slate-100 flex min-h-screen">

    <!-- SIDEBAR NAVIGATION PANEL -->
    <aside class="w-64 bg-slate-800 border-r border-slate-700 flex flex-col justify-between">
        <div class="p-6">
            <h2 class="text-xl font-bold text-indigo-400 tracking-wide mb-8">Admin Console</h2>
            <nav class="space-y-2">
                <a href="index.php" class="block px-4 py-2.5 text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">Dashboard Hub</a>
                <a href="gallery.php" class="block px-4 py-2.5 bg-indigo-600/20 text-indigo-400 font-medium rounded-lg border-l-4 border-indigo-500">Manage Gallery</a>
                <a href="events.php" class="block px-4 py-2.5 text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">Manage Events</a>
                <a href="blog.php" class="block px-4 py-2.5 text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">Manage Blogs</a>
                <a href="testimonies.php" class="block px-4 py-2.5 text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">Testimonies</a>
                <a href="team.php" class="block px-4 py-2.5 text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">Manage Team</a>
                <a href="homepage.php" class="block px-4 py-2.5 text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">Manage Homepage</a>
                <a href="donations.php" class="block px-4 py-2.5 text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">Donation Settings</a>
            </nav>
        </div>
        <div class="p-4 border-t border-slate-700">
            <a href="logout.php" class="block text-center bg-rose-600/20 hover:bg-rose-600 text-rose-400 hover:text-white text-sm font-semibold py-2 rounded-lg transition-colors cursor-pointer">Log Out</a>
        </div>
    </aside>

    <!-- CONTENT WRAPPER -->
    <main class="flex-1 p-8 overflow-y-auto">
        <header class="mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight">Ministry Photo Gallery</h1>
            <p class="text-slate-400 text-sm mt-1">Upload and coordinate the media photos of your annual ministry functions.</p>
        </header>

        <!-- Notification Alerts -->
        <?php if (!empty($success)): ?>
            <div class="bg-emerald-500/10 border border-emerald-500 text-emerald-400 p-4 rounded-lg mb-6 text-sm">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="bg-rose-500/10 border border-rose-500 text-rose-400 p-4 rounded-lg mb-6 text-sm">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- DYNAMIC UPLOAD UMTILITY FORM -->
            <div class="bg-slate-800 border border-slate-700 p-6 rounded-xl shadow-md h-fit">
                <h3 class="text-lg font-bold border-b border-slate-700 pb-3 mb-4 text-indigo-400">Upload New Photo</h3>
                
                <!-- enctype attribute is absolutely required to process file uploads -->
                <form action="gallery.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Caption / Image Title</label>
                        <input type="text" name="title" placeholder="e.g., Scholastic distribution at Grace Primary School" 
                               class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Activity Category</label>
                        <select name="category" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500">
                            <option value="Crusades">Crusades</option>
                            <option value="Evangelism">Evangelism</option>
                            <option value="Scholastic Outreach">Scholastic Outreach</option>
                            <option value="Community Aid">Community Aid</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Select File</label>
                        <input type="file" name="gallery_file" required 
                               class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600/20 file:text-indigo-400 hover:file:bg-indigo-600/30 file:cursor-pointer">
                    </div>

                    <button type="submit" name="upload_image" 
                            class="w-full bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors cursor-pointer shadow-md">
                        Upload to Gallery
                    </button>
                </form>
            </div>

            <!-- MEDIA LIVE STREAMING VIEW -->
            <div class="lg:col-span-2 bg-slate-800 border border-slate-700 p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-bold border-b border-slate-700 pb-3 mb-4">Live Photo Inventory</h3>
                
                <?php if (empty($images)): ?>
                    <p class="text-slate-400 text-sm py-8 text-center">No photos have been added to the dynamic repository yet.</p>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach ($images as $img): ?>
                            <div class="bg-slate-900 border border-slate-700 rounded-lg overflow-hidden flex flex-col justify-between">
                                <div class="relative h-40 bg-slate-950 flex items-center justify-center overflow-hidden">
                                    <!-- Dynamic path pulled from the server -->
                                    <img src="../<?php echo htmlspecialchars($img['image_path']); ?>" alt="Gallery Image" class="w-full h-full object-cover">
                                    <span class="absolute top-2 left-2 bg-slate-900/80 backdrop-blur-xs text-xs px-2 py-0.5 rounded text-indigo-400 font-semibold border border-slate-700">
                                        <?php echo htmlspecialchars($img['category']); ?>
                                    </span>
                                </div>
                                <div class="p-4 space-y-3">
                                    <p class="text-sm font-medium text-white line-clamp-2">
                                        <?php echo htmlspecialchars($img['title'] ?: 'Untitled Activity Image'); ?>
                                    </p>
                                    <div class="flex items-center justify-between pt-2 border-t border-slate-800">
                                        <span class="text-xs text-slate-500"><?php echo date('M d, Y', strtotime($img['created_at'])); ?></span>
                                        <a href="gallery.php?delete=<?php echo $img['id']; ?>" 
                                           onclick="return confirm('Are you sure you want to permanently delete this photo?');" 
                                           class="text-xs font-bold text-rose-400 hover:text-rose-300 transition-colors">
                                            Delete Photo
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

</body>
</html>