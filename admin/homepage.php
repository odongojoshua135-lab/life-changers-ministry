<?php
// admin/homepage.php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$success = '';
$error = '';

// --- Handle Form Updates ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_homepage'])) {
    $hero_title     = trim($_POST['hero_title']);
    $hero_subtitle  = trim($_POST['hero_subtitle']);
    $welcome_message = trim($_POST['welcome_message']);
    $about_summary  = trim($_POST['about_summary']);

    if (empty($hero_title)) {
        $error = 'The Main Hero Title field cannot be empty.';
    } else {
        $stmt = $pdo->prepare("UPDATE homepage_settings SET hero_title = :hero_title, hero_subtitle = :hero_subtitle, welcome_message = :welcome_message, about_summary = :about_summary WHERE id = 1");
        $stmt->execute([
            'hero_title'     => $hero_title,
            'hero_subtitle'  => $hero_subtitle,
            'welcome_message' => $welcome_message,
            'about_summary'  => $about_summary
        ]);
        $success = 'Homepage landing text assets successfully updated.';
    }
}

// Fetch current values
$settings = $pdo->query("SELECT * FROM homepage_settings WHERE id = 1")->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Homepage - Life Changers Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-900 text-slate-100 flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-slate-800 border-r border-slate-700 flex flex-col justify-between">
        <div class="p-6">
            <h2 class="text-xl font-bold text-indigo-400 tracking-wide mb-8">Admin Console</h2>
            <nav class="space-y-2">
                <a href="index.php" class="block px-4 py-2.5 text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">Dashboard Hub</a>
                <a href="gallery.php" class="block px-4 py-2.5 text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">Manage Gallery</a>
                <a href="events.php" class="block px-4 py-2.5 text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">Manage Events</a>
                <a href="blog.php" class="block px-4 py-2.5 text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">Manage Blogs</a>
                <a href="testimonies.php" class="block px-4 py-2.5 text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">Testimonies</a>
                <a href="team.php" class="block px-4 py-2.5 text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">Manage Team</a>
                <a href="homepage.php" class="block px-4 py-2.5 bg-indigo-600/20 text-indigo-400 font-medium rounded-lg border-l-4 border-indigo-500">Manage Homepage</a>
                <a href="donations.php" class="block px-4 py-2.5 text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">Donation Settings</a>
            </nav>
        </div>
        <div class="p-4 border-t border-slate-700">
            <a href="logout.php" class="block text-center bg-rose-600/20 hover:bg-rose-600 text-rose-400 hover:text-white text-sm font-semibold py-2 rounded-lg transition-colors">Log Out</a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 p-8 overflow-y-auto">
        <header class="mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight">Manage Homepage Content</h1>
            <p class="text-slate-400 text-sm mt-1">Dynamically alter the main hero sections and introductory text blocks on your public homepage landing layout.</p>
        </header>

        <?php if (!empty($success)): ?>
            <div class="bg-emerald-500/10 border border-emerald-500 text-emerald-400 p-4 rounded-lg mb-6 text-sm"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="bg-rose-500/10 border border-rose-500 text-rose-400 p-4 rounded-lg mb-6 text-sm"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="max-w-3xl bg-slate-800 border border-slate-700 p-6 rounded-xl shadow-md">
            <form action="homepage.php" method="POST" class="space-y-5">
                
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Main Hero Title</label>
                    <input type="text" name="hero_title" value="<?php echo htmlspecialchars($settings['hero_title'] ?? ''); ?>" required
                           class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Hero Subtitle / Subtext</label>
                    <textarea name="hero_subtitle" rows="2"
                              class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500"><?php echo htmlspecialchars($settings['hero_subtitle'] ?? ''); ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Welcome Message Callout</label>
                    <textarea name="welcome_message" rows="3"
                              class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500"><?php echo htmlspecialchars($settings['welcome_message'] ?? ''); ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">About Us Section Summary Text</label>
                    <textarea name="about_summary" rows="4"
                              class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:border-indigo-500"><?php echo htmlspecialchars($settings['about_summary'] ?? ''); ?></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" name="update_homepage" 
                            class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold py-2.5 px-6 rounded-lg transition-colors cursor-pointer shadow-md">
                        Save Homepage Changes
                    </button>
                </div>
            </form>
        </div>
    </main>

</body>
</html>