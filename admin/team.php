<?php
// admin/team.php
session_start();
require_once '../config/db.php';

// Authentication Guard
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$success = '';
$error = '';

// Form tracking control variables
$editMode = false;
$editId = 0;
$name = '';
$role = '';
$facebook_url = '';
$twitter_url = '';
$photo_path = '';

// --- 1. POST ACTION INTERFACES (CREATE & UPDATE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_member'])) {
    $name         = trim($_POST['name']);
    $role         = trim($_POST['role']);
    $facebook_url = trim($_POST['facebook_url']);
    $twitter_url  = trim($_POST['twitter_url']);
    $editId       = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
    
    $db_photo_path = isset($_POST['existing_photo']) ? $_POST['existing_photo'] : '';

    // Process profile picture file if uploaded
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_photo']['tmp_name'];
        $fileName = $_FILES['profile_photo']['name'];
        $fileSize = $_FILES['profile_photo']['size'];
        
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($fileExtension, $allowedExtensions)) {
            if ($fileSize <= 3145728) { // 3MB cap for profile optimization
                $uploadDir = '../uploads/team/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // Erase the old photo file if updating the member profile picture
                if (!empty($db_photo_path) && file_exists('../' . $db_photo_path)) {
                    unlink('../' . $db_photo_path);
                }

                $newFileName = time() . '_member.' . $fileExtension;
                $dest_path = $uploadDir . $newFileName;
                
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $db_photo_path = 'uploads/team/' . $newFileName;
                }
            } else {
                $error = 'Profile headshot size exceeds the 3MB ceiling optimized limit.';
            }
        } else {
            $error = 'Invalid image extension type. Use JPG, PNG, or WEBP.';
        }
    }

    if (empty($error)) {
        if ($editId > 0) {
            // Update an active team record
            $stmt = $pdo->prepare("UPDATE team SET name = :name, role = :role, photo_path = :photo_path, facebook_url = :facebook_url, twitter_url = :twitter_url WHERE id = :id");
            $stmt->execute([
                'name'         => $name,
                'role'         => $role,
                'photo_path'   => $db_photo_path,
                'facebook_url' => $facebook_url,
                'twitter_url'  => $twitter_url,
                'id'           => $editId
            ]);
            $success = 'Staff profile matrix configured successfully.';
        } else {
            // Insert a new member record
            $stmt = $pdo->prepare("INSERT INTO team (name, role, photo_path, facebook_url, twitter_url) VALUES (:name, :role, :photo_path, :facebook_url, :twitter_url)");
            $stmt->execute([
                'name'         => $name,
                'role'         => $role,
                'photo_path'   => $db_photo_path,
                'facebook_url' => $facebook_url,
                'twitter_url'  => $twitter_url
            ]);
            $success = 'New ministry leader profile published.';
        }
        
        // Wipe local form variables
        $name = $role = $facebook_url = $twitter_url = '';
    }
}

// --- 2. RETRIEVE RECORD DATA INTO MODIFICATION STATE ---
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM team WHERE id = :id");
    $stmt->execute(['id' => $editId]);
    $member = $stmt->fetch();
    
    if ($member) {
        $editMode     = true;
        $name         = $member['name'];
        $role         = $member['role'];
        $facebook_url = $member['facebook_url'];
        $twitter_url  = $member['twitter_url'];
        $photo_path   = $member['photo_path'];
    }
}

// --- 3. HARD REMOVAL CRITERIA ROUTINES (DELETE) ---
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    
    $stmt = $pdo->prepare("SELECT photo_path FROM team WHERE id = :id");
    $stmt->execute(['id' => $deleteId]);
    $member = $stmt->fetch();
    
    if ($member) {
        if (!empty($member['photo_path']) && file_exists('../' . $member['photo_path'])) {
            unlink('../' . $member['photo_path']);
        }
        
        $delStmt = $pdo->prepare("DELETE FROM team WHERE id = :id");
        $delStmt->execute(['id' => $deleteId]);
        $success = 'Staff profile removed from registry catalog files.';
    }
}

// Retrieve complete roster order listing
$teamRoster = $pdo->query("SELECT * FROM team ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Team - Life Changers Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-900 text-slate-100 flex min-h-screen">

    <!-- SIDEBAR NAVIGATION PANEL -->
    <aside class="w-64 bg-slate-800 border-r border-slate-700 flex flex-col justify-between">
        <div class="p-6">
            <h2 class="text-xl font-bold text-indigo-400 tracking-wide mb-8">Admin Console</h2>
            <nav class="space-y-2">
                <a href="index.php" class="block px-4 py-2.5 text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">Dashboard Hub</a>
                <a href="gallery.php" class="block px-4 py-2.5 text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">Manage Gallery</a>
                <a href="events.php" class="block px-4 py-2.5 text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">Manage Events</a>
                <a href="blog.php" class="block px-4 py-2.5 text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">Manage Blogs</a>
                <a href="testimonies.php" class="block px-4 py-2.5 text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">Testimonies</a>
                <a href="team.php" class="block px-4 py-2.5 bg-indigo-600/20 text-indigo-400 font-medium rounded-lg border-l-4 border-indigo-500">Manage Team</a>
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
            <h1 class="text-3xl font-extrabold tracking-tight">Ministry Team Roster</h1>
            <p class="text-slate-400 text-sm mt-1">Configure profile matrices for administrators, leaders, and coordinating staff members.</p>
        </header>

        <!-- Notification Alerts -->
        <?php if (!empty($success)): ?>
            <div class="bg-emerald-500/10 border border-emerald-500 text-emerald-400 p-4 rounded-lg mb-6 text-sm"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="bg-rose-500/10 border border-rose-500 text-rose-400 p-4 rounded-lg mb-6 text-sm"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- ROSTER INPUT INTERFACE MANAGEMENT FORM -->
            <div class="bg-slate-800 border border-slate-700 p-6 rounded-xl shadow-md h-fit">
                <h3 class="text-lg font-bold border-b border-slate-700 pb-3 mb-4 text-indigo-400">
                    <?php echo $editMode ? 'Modify Profile' : 'Register Team Member'; ?>
                </h3>
                
                <form action="team.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <?php if ($editMode): ?>
                        <input type="hidden" name="edit_id" value="<?php echo $editId; ?>">
                        <input type="hidden" name="existing_photo" value="<?php echo htmlspecialchars($photo_path); ?>">
                    <?php endif; ?>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Full Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required placeholder="e.g., Pastor Joram" 
                               class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Ministry Role / Title</label>
                        <input type="text" name="role" value="<?php echo htmlspecialchars($role); ?>" required placeholder="e.g., Founder & Senior Pastor" 
                               class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Facebook URL (Optional)</label>
                        <input type="url" name="facebook_url" value="<?php echo htmlspecialchars($facebook_url); ?>" placeholder="https://facebook.com/username" 
                               class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Twitter / X URL (Optional)</label>
                        <input type="url" name="twitter_url" value="<?php echo htmlspecialchars($twitter_url); ?>" placeholder="https://x.com/username" 
                               class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Profile Headshot Photo</label>
                        <input type="file" name="profile_photo" 
                               class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600/20 file:text-indigo-400 hover:file:bg-indigo-600/30 file:cursor-pointer">
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button type="submit" name="save_member" 
                                class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold py-2.5 rounded-lg transition-colors cursor-pointer text-center shadow-md">
                            <?php echo $editMode ? 'Apply Updates' : 'Confirm Registration'; ?>
                        </button>
                        <?php if ($editMode): ?>
                            <a href="team.php" class="bg-slate-700 hover:bg-slate-600 text-slate-200 text-sm font-semibold py-2.5 px-4 rounded-lg transition-colors text-center">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- VISUAL ROSTER DECK DISPLAY GRID -->
            <div class="lg:col-span-2 bg-slate-800 border border-slate-700 p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-bold border-b border-slate-700 pb-3 mb-4">Active Staff Profiles</h3>
                
                <?php if (empty($teamRoster)): ?>
                    <p class="text-slate-400 text-sm py-12 text-center">No active team members registered on the platform roster yet.</p>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach ($teamRoster as $mbr): ?>
                            <div class="bg-slate-900 border border-slate-700 rounded-xl p-4 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <?php if (!empty($mbr['photo_path'])): ?>
                                        <img src="../<?php echo htmlspecialchars($mbr['photo_path']); ?>" class="w-16 h-16 rounded-full object-cover border-2 border-slate-700 flex-shrink-0">
                                    <?php else: ?>
                                        <div class="w-16 h-16 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-xl font-bold text-indigo-400 flex-shrink-0">
                                            <?php echo strtoupper(substr($mbr['name'], 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div>
                                        <h4 class="font-bold text-white text-base leading-tight"><?php echo htmlspecialchars($mbr['name']); ?></h4>
                                        <p class="text-xs text-indigo-400 font-medium mt-0.5"><?php echo htmlspecialchars($mbr['role']); ?></p>
                                        
                                        <div class="flex items-center gap-2 mt-2">
                                            <?php if(!empty($mbr['facebook_url'])): ?>
                                                <span class="text-[10px] bg-slate-800 px-1.5 py-0.5 rounded text-slate-400">FB</span>
                                            <?php endif; ?>
                                            <?php if(!empty($mbr['twitter_url'])): ?>
                                                <span class="text-[10px] bg-slate-800 px-1.5 py-0.5 rounded text-slate-400">X</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-1.5 shrink-0">
                                    <a href="team.php?edit=<?php echo $mbr['id']; ?>" 
                                       class="text-xs font-bold text-center bg-slate-800 hover:bg-slate-700 border border-slate-700 py-1 px-2.5 rounded transition-colors text-slate-300">
                                        Modify
                                    </a>
                                    <a href="team.php?delete=<?php echo $mbr['id']; ?>" 
                                       onclick="return confirm('Remove this team profile from the database ledger completely?');" 
                                       class="text-xs font-bold text-center bg-rose-950/30 hover:bg-rose-600 text-rose-400 hover:text-white py-1 px-2.5 rounded border border-rose-900/30 transition-colors">
                                        Remove
                                    </a>
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