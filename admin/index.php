<?php
// admin/index.php
session_start();
require_once '../config/db.php';

// Authentication Guard: Redirect unauthorized sessions instantly
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Navigation variables for the shared sidebar & header layout
$admin_page = 'dashboard';
$page_title = 'Dashboard Overview';

// Gather dynamic metric dashboard counts from tables
$photoCount = $pdo->query("SELECT COUNT(*) FROM gallery")->fetchColumn();
$eventCount = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$blogCount  = $pdo->query("SELECT COUNT(*) FROM blogs")->fetchColumn();
$testimonyCount = $pdo->query("SELECT COUNT(*) FROM testimonies WHERE status = 'Pending'")->fetchColumn();

// Count distinct dynamic categories being managed as individual galleries
$galleryCount = $pdo->query("SELECT COUNT(DISTINCT category) FROM gallery")->fetchColumn();

// Fetch latest activities to show on the dashboard feed
$latestStmt = $pdo->query("SELECT title, category, created_at FROM gallery ORDER BY id DESC LIMIT 4");
$latestActivities = $latestStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Hub - Life Changers Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0b1329] text-slate-200 font-sans antialiased min-h-screen flex">

    <!-- 1. INCLUDE SHARED SIDEBAR NAVIGATION -->
    <?php 
    $sidebar_path = __DIR__ . '/includes/admin-sidebar.php';
    if (file_exists($sidebar_path)) {
        include_once $sidebar_path;
    } else {
        include_once __DIR__ . '/admin-sidebar.php';
    }
    ?>

    <!-- 2. PRIMARY SCREEN VIEW PORTAL -->
    <div class="max-w-6xl mx-auto w-full">

        <header class="mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight text-white">Dashboard Overview</h1>
            <p class="text-slate-400 text-sm mt-1">Real-time content management stats for Life Changers for Christ Ministry.</p>
        </header>

        <!-- Dynamic Stat Metric Cards Layout -->
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <div class="bg-[#121a35] border border-slate-800 p-5 rounded-xl shadow-md">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Photos</div>
                <div class="text-3xl font-black text-white mt-2"><?php echo $photoCount; ?></div>
            </div>
            <div class="bg-[#121a35] border border-slate-800 p-5 rounded-xl shadow-md">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Galleries</div>
                <div class="text-3xl font-black text-indigo-400 mt-2"><?php echo $galleryCount; ?></div>
            </div>
            <div class="bg-[#121a35] border border-slate-800 p-5 rounded-xl shadow-md">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Events</div>
                <div class="text-3xl font-black text-white mt-2"><?php echo $eventCount; ?></div>
            </div>
            <div class="bg-[#121a35] border border-slate-800 p-5 rounded-xl shadow-md">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Blog Posts</div>
                <div class="text-3xl font-black text-white mt-2"><?php echo $blogCount; ?></div>
            </div>
            <div class="bg-[#121a35] border border-slate-800 p-5 rounded-xl shadow-md">
                <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Pending Reviews</div>
                <div class="text-3xl font-black text-amber-400 mt-2"><?php echo $testimonyCount; ?></div>
            </div>
        </section>

        <!-- Activities & Quick Management Portal Matrix -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-[#121a35] border border-slate-800 p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-bold border-b border-slate-800 pb-3 mb-4 text-indigo-400">Latest Image Updates</h3>
                <?php if (empty($latestActivities)): ?>
                    <p class="text-slate-400 text-sm py-4">No photos uploaded yet. Head over to the Gallery manager to add some!</p>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($latestActivities as $activity): ?>
                            <div class="flex items-center justify-between bg-slate-900/50 p-3 rounded-lg border border-slate-800">
                                <div>
                                    <h4 class="font-medium text-sm text-white"><?php echo htmlspecialchars($activity['title'] ?: 'Untitled Image'); ?></h4>
                                    <span class="text-xs text-indigo-400"><?php echo htmlspecialchars($activity['category']); ?></span>
                                </div>
                                <span class="text-xs text-slate-500"><?php echo date('M d, Y', strtotime($activity['created_at'])); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="bg-[#121a35] border border-slate-800 p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-bold border-b border-slate-800 pb-3 mb-4 text-indigo-400">Quick Shortcuts</h3>
                <div class="space-y-3">
                    <a href="gallery.php" class="block text-center bg-slate-800/80 hover:bg-slate-700 font-medium text-sm py-2.5 rounded-lg transition-colors">Upload New Photos</a>
                    <a href="events.php" class="block text-center bg-slate-800/80 hover:bg-slate-700 font-medium text-sm py-2.5 rounded-lg transition-colors">Schedule Crusade / Outreach</a>
                    <a href="blog.php" class="block text-center bg-slate-800/80 hover:bg-slate-700 font-medium text-sm py-2.5 rounded-lg transition-colors">Compose Blog Post</a>
                </div>
            </div>
        </section>

    </div>

    <!-- CLOSE WRAPPER TAGS OPENED BY ADMIN-SIDEBAR.PHP -->
    </main>
</div>

</body>
</html>