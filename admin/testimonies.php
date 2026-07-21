<?php
// admin/testimonies.php
session_start();
require_once '../config/db.php';

// Authentication Guard
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$success = '';
$error = '';

// --- HANDLE APPROVAL ACTION ---
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    $stmt = $pdo->prepare("UPDATE testimonies SET status = 'approved' WHERE id = :id");
    if ($stmt->execute(['id' => $id])) {
        $success = 'Testimony approved and published to the live site!';
    } else {
        $error = 'Failed to update testimony status.';
    }
}

// --- HANDLE REJECT / HIDE ACTION ---
if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    $stmt = $pdo->prepare("UPDATE testimonies SET status = 'pending' WHERE id = :id");
    if ($stmt->execute(['id' => $id])) {
        $success = 'Testimony rejected/hidden from the public view.';
    } else {
        $error = 'Failed to update testimony status.';
    }
}

// --- HANDLE PERMANENT DELETION ---
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM testimonies WHERE id = :id");
    if ($stmt->execute(['id' => $id])) {
        $success = 'Testimony permanently deleted from the database.';
    } else {
        $error = 'Failed to delete testimony record.';
    }
}

// Fetch testimonies split by their operational status (Latest submissions first)
$testimonies = $pdo->query("SELECT * FROM testimonies ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Testimonies - Life Changers Admin</title>
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
                <a href="testimonies.php" class="block px-4 py-2.5 bg-indigo-600/20 text-indigo-400 font-medium rounded-lg border-l-4 border-indigo-500">Testimonies</a>
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
            <h1 class="text-3xl font-extrabold tracking-tight">Testimonies Moderation Queue</h1>
            <p class="text-slate-400 text-sm mt-1">Review, approve, or reject user-submitted stories of faith before they are visible publicly.</p>
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

        <!-- TESTIMONIES DISPLAY MATRIX -->
        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 shadow-md">
            <h3 class="text-lg font-bold border-b border-slate-700 pb-3 mb-6">Submissions Ledger</h3>

            <?php if (empty($testimonies)): ?>
                <p class="text-slate-400 text-sm py-12 text-center">No community testimonies have been submitted to the database yet.</p>
            <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($testimonies as $test): ?>
                        <div class="border border-slate-700 bg-slate-900 rounded-xl p-5 flex flex-col md:flex-row justify-between items-start gap-4 transition-all hover:border-slate-600">
                            
                            <div class="space-y-2 flex-1">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <h4 class="font-bold text-white text-lg"><?php echo htmlspecialchars($test['name']); ?></h4>
                                    
                                    <!-- Dynamic Badge Status Labels -->
                                    <?php if ($test['status'] === 'approved'): ?>
                                        <span class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                            Live on Site
                                        </span>
                                    <?php else: ?>
                                        <span class="bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                            Pending Moderation
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Added Testimony Title Field -->
                                <div class="text-xs text-indigo-400 font-semibold tracking-wide uppercase">
                                    Title: <?php echo htmlspecialchars($test['title']); ?>
                                </div>

                                <p class="text-xs text-slate-500 font-medium">
                                    Submitted: <?php echo date('M d, Y \a\t g:i A', strtotime($test['created_at'])); ?>
                                </p>
                                
                                <p class="text-sm text-slate-300 leading-relaxed pt-1 italic whitespace-pre-line">
                                    "<?php echo htmlspecialchars($test['message']); ?>"
                                </p>
                            </div>

                            <!-- ACTION CONTROLS INTERFACE -->
                            <div class="flex md:flex-col gap-2 w-full md:w-auto border-t md:border-t-0 border-slate-800 pt-4 md:pt-0 justify-end shrink-0">
                                <?php if ($test['status'] !== 'approved'): ?>
                                    <a href="testimonies.php?approve=<?php echo $test['id']; ?>" 
                                       class="flex-1 md:w-28 text-center bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold py-2 px-3 rounded-lg transition-colors cursor-pointer shadow-sm shadow-emerald-950">
                                         Approve & Publish
                                    </a>
                                <?php else: ?>
                                    <a href="testimonies.php?reject=<?php echo $test['id']; ?>" 
                                       class="flex-1 md:w-28 text-center bg-amber-600/20 hover:bg-amber-600 text-amber-400 hover:text-white text-xs font-bold py-2 px-3 rounded-lg transition-colors border border-amber-500/30 cursor-pointer">
                                         Hide / Revoke
                                    </a>
                                <?php endif; ?>

                                <a href="testimonies.php?delete=<?php echo $test['id']; ?>" 
                                   onclick="return confirm('Are you sure you want to permanently delete this testimony from database historical records?');" 
                                   class="flex-1 md:w-28 text-center bg-rose-600/10 hover:bg-rose-600 text-rose-400 hover:text-white text-xs font-bold py-2 px-3 rounded-lg transition-colors border border-rose-500/20 cursor-pointer">
                                    Delete Record
                                </a>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>