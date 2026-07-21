<?php
// admin/blog.php
session_start();
require_once '../config/db.php';

// Authentication Guard
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Sidebar & Header Configuration Variables
$admin_page = 'blogs';
$page_title = 'Ministry Blog Publisher';

$success = '';
$error = '';

// Form state control variables
$editMode = false;
$editId = 0;
$title = '';
$content = '';
$author = '';
$cover_path = '';

// Helper function to turn standard titles into clean URL slugs
function generateSlug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

// --- 1. POST ACTION ROUTINES (CREATE & UPDATE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_blog'])) {
    $title   = trim($_POST['title']);
    $content = trim($_POST['content']);
    $author  = trim($_POST['author']) ?: 'Ministry Team';
    $editId  = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
    $slug    = generateSlug($title);
    
    $db_cover_path = isset($_POST['existing_cover']) ? $_POST['existing_cover'] : '';

    // Handle featured image file submission block
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['cover_image']['tmp_name'];
        $fileName = $_FILES['cover_image']['name'];
        $fileSize = $_FILES['cover_image']['size'];
        
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($fileExtension, $allowedExtensions)) {
            if ($fileSize <= 5242880) { // 5MB Limit Max
                $uploadDir = '../uploads/blog/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // Drop previous asset layout if replacing cover image
                if (!empty($db_cover_path) && file_exists('../' . $db_cover_path)) {
                    unlink('../' . $db_cover_path);
                }

                $newFileName = time() . '_blog.' . $fileExtension;
                $dest_path = $uploadDir . $newFileName;
                
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $db_cover_path = 'uploads/blog/' . $newFileName;
                }
            } else {
                $error = 'Cover thumbnail file size exceeds 5MB.';
            }
        } else {
            $error = 'Invalid file extension. Please select a JPG, PNG, or WEBP image.';
        }
    }

    if (empty($error)) {
        if ($editId > 0) {
            // Update an active article row
            $stmt = $pdo->prepare("UPDATE blogs SET title = :title, slug = :slug, content = :content, author = :author, cover_image = :cover_image WHERE id = :id");
            $stmt->execute([
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'author' => $author,
                'cover_image' => $db_cover_path,
                'id' => $editId
            ]);
            $success = 'Blog article updated and published successfully.';
        } else {
            // Inject a brand new article row
            $stmt = $pdo->prepare("INSERT INTO blogs (title, slug, content, author, cover_image) VALUES (:title, :slug, :content, :author, :cover_image)");
            $stmt->execute([
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'author' => $author,
                'cover_image' => $db_cover_path
            ]);
            $success = 'New blog post published live to the platform catalog.';
        }
        
        // Reset states to empty input boxes
        $title = $content = $author = '';
    }
}

// --- 2. RETRIEVE RECORD DATA INTO MODIFICATION FIELDS ---
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = :id");
    $stmt->execute(['id' => $editId]);
    $post = $stmt->fetch();
    
    if ($post) {
        $editMode = true;
        $title = $post['title'];
        $content = $post['content'];
        $author = $post['author'];
        $cover_path = $post['cover_image'];
    }
}

// --- 3. HARD REMOVAL CRITERIA ROUTINES (DELETE) ---
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    
    $stmt = $pdo->prepare("SELECT cover_image FROM blogs WHERE id = :id");
    $stmt->execute(['id' => $deleteId]);
    $post = $stmt->fetch();
    
    if ($post) {
        if (!empty($post['cover_image']) && file_exists('../' . $post['cover_image'])) {
            unlink('../' . $post['cover_image']);
        }
        
        $delStmt = $pdo->prepare("DELETE FROM blogs WHERE id = :id");
        $delStmt->execute(['id' => $deleteId]);
        $success = 'Blog article successfully removed from registry storage.';
    }
}

// Pull all available articles, latest updates up top
$blogsList = $pdo->query("SELECT * FROM blogs ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blogs - Life Changers Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0b1329] text-slate-200 font-sans antialiased min-h-screen flex">

    <!-- INCLUDE SHARED SIDEBAR -->
    <?php 
    $sidebar_path = __DIR__ . '/includes/admin-sidebar.php';
    if (file_exists($sidebar_path)) {
        include_once $sidebar_path;
    } else {
        include_once __DIR__ . '/admin-sidebar.php';
    }
    ?>

    <!-- CONTENT WRAPPER HUB -->
    <div class="max-w-6xl mx-auto w-full">
        <header class="mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight text-white">Ministry Blog Publisher</h1>
            <p class="text-slate-400 text-sm mt-1">Compose, structure headlines, and modify educational articles or text recaps of your events.</p>
        </header>

        <!-- Notification Alerts -->
        <?php if (!empty($success)): ?>
            <div class="bg-emerald-950/60 border border-emerald-500/30 text-emerald-300 p-4 rounded-xl mb-6 text-xs font-semibold">✓ <?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="bg-rose-950/60 border border-rose-500/30 text-rose-300 p-4 rounded-xl mb-6 text-xs font-semibold">⚠ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- ARTICLE EDITOR INTERFACE PANEL FORM -->
            <div class="bg-[#121a35] border border-slate-800 p-6 rounded-2xl shadow-xl h-fit lg:col-span-1">
                <h3 class="text-sm font-bold border-b border-slate-800 pb-3 mb-4 text-indigo-400 uppercase tracking-wider">
                    <?php echo $editMode ? 'Edit Blog Post' : 'Compose Blog Post'; ?>
                </h3>
                
                <form action="blog.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <?php if ($editMode): ?>
                        <input type="hidden" name="edit_id" value="<?php echo $editId; ?>">
                        <input type="hidden" name="existing_cover" value="<?php echo htmlspecialchars($cover_path); ?>">
                    <?php endif; ?>

                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Post Title / Headline</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required placeholder="e.g., Transforming Hearts in Jinja" 
                               class="w-full bg-[#0b1329] border border-slate-800 rounded-lg px-3 py-2 text-white text-xs focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Author Name</label>
                        <input type="text" name="author" value="<?php echo htmlspecialchars($author ?: 'Pastor / Admin'); ?>" placeholder="e.g., Office of Administration" 
                               class="w-full bg-[#0b1329] border border-slate-800 rounded-lg px-3 py-2 text-white text-xs focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Body Text Content</label>
                        <textarea name="content" rows="8" required placeholder="Write your full message or news breakdown details here..." 
                                  class="w-full bg-[#0b1329] border border-slate-800 rounded-lg px-3 py-2 text-white text-xs focus:outline-none focus:border-indigo-500 font-sans leading-relaxed"><?php echo htmlspecialchars($content); ?></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">Featured Cover Banner</label>
                        <input type="file" name="cover_image" 
                               class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-600/20 file:text-indigo-400 hover:file:bg-indigo-600/30 file:cursor-pointer">
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button type="submit" name="save_blog" 
                                class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold py-2.5 rounded-lg transition-colors cursor-pointer text-center shadow-md">
                            <?php echo $editMode ? 'Save Changes' : 'Publish Article'; ?>
                        </button>
                        <?php if ($editMode): ?>
                            <a href="blog.php" class="bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold py-2.5 px-4 rounded-lg transition-colors text-center">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- BLOG INVENTORY LEDGER CHANNELS -->
            <div class="lg:col-span-2 bg-[#121a35] border border-slate-800 p-6 rounded-2xl shadow-xl">
                <h3 class="text-sm font-bold border-b border-slate-800 pb-3 mb-4 text-indigo-400 uppercase tracking-wider">Published Articles</h3>
                
                <?php if (empty($blogsList)): ?>
                    <p class="text-slate-400 text-xs py-12 text-center">Your ministry hasn't written any blog entries yet.</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($blogsList as $post): ?>
                            <div class="bg-[#0b1329] border border-slate-800/80 rounded-xl p-4 flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
                                <div class="flex gap-4 items-center">
                                    <?php if (!empty($post['cover_image'])): ?>
                                        <img src="../<?php echo htmlspecialchars($post['cover_image']); ?>" class="w-16 h-16 rounded-lg object-cover border border-slate-800 flex-shrink-0">
                                    <?php else: ?>
                                        <div class="w-16 h-16 rounded-lg bg-[#121a35] border border-slate-800 flex items-center justify-center text-[10px] text-slate-500 font-bold flex-shrink-0">No Cover</div>
                                    <?php endif; ?>
                                    
                                    <div>
                                        <h4 class="font-bold text-sm text-white"><?php echo htmlspecialchars($post['title']); ?></h4>
                                        <p class="text-xs text-indigo-400 mt-0.5 font-medium">
                                            ✍️ By <?php echo htmlspecialchars($post['author']); ?> | 🗓️ <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                                        </p>
                                        <p class="text-xs text-slate-500 font-mono mt-0.5">Slug: /blog/<?php echo htmlspecialchars($post['slug']); ?></p>
                                        <p class="text-xs text-slate-400 mt-1 line-clamp-2 max-w-xl"><?php echo htmlspecialchars($post['content']); ?></p>
                                    </div>
                                </div>

                                <div class="flex sm:flex-col gap-2 w-full sm:w-auto border-t sm:border-t-0 border-slate-800 pt-3 sm:pt-0 justify-end">
                                    <a href="blog.php?edit=<?php echo $post['id']; ?>" 
                                       class="text-xs font-bold text-center bg-slate-800 hover:bg-slate-700 border border-slate-700 py-1.5 px-3 rounded-lg transition-colors text-slate-300">
                                        Edit
                                    </a>
                                    <a href="blog.php?delete=<?php echo $post['id']; ?>" 
                                       onclick="return confirm('Permanently delete this blog article from the server?');" 
                                       class="text-xs font-bold text-center bg-rose-950/60 hover:bg-rose-900 border border-rose-800/50 py-1.5 px-3 rounded-lg transition-colors text-rose-300 hover:text-white">
                                        Delete
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- CLOSE WRAPPER TAGS OPENED BY ADMIN-SIDEBAR.PHP -->
    </main>
</div>

</body>
</html>