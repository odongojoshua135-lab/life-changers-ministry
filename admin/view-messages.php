<?php
// admin/view-messages.php
session_start();
require_once '../config/db.php';

// Authentication Guard
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$success = '';
$error = '';

// Handle Message Deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = :id");
    if ($stmt->execute(['id' => $id])) {
        $success = 'Message permanently deleted.';
    } else {
        $error = 'Failed to delete message.';
    }
}

// Fetch all messages (Newest first)
$messages = $pdo->query("SELECT * FROM messages ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox Messages - Life Changers Admin</title>
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
                <a href="view-messages.php" class="block px-4 py-2.5 bg-indigo-600/20 text-indigo-400 font-medium rounded-lg border-l-4 border-indigo-500">Inbox Messages</a>
                <a href="team.php" class="block px-4 py-2.5 text-slate-300 hover:bg-slate-700 rounded-lg transition-colors">Manage Team</a>
            </nav>
        </div>
        <div class="p-4 border-t border-slate-700">
            <a href="logout.php" class="block text-center bg-rose-600/20 hover:bg-rose-600 text-rose-400 hover:text-white text-sm font-semibold py-2 rounded-lg transition-colors">Log Out</a>
        </div>
    </aside>

    <!-- CONTENT WRAPPER -->
    <main class="flex-1 p-8 overflow-y-auto">
        <header class="mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight">Inbox Messages</h1>
            <p class="text-slate-400 text-sm mt-1">Review and manage inquiries received from the website contact page.</p>
        </header>

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

        <div class="bg-slate-800 border border-slate-700 rounded-xl p-6 shadow-md">
            <h3 class="text-lg font-bold border-b border-slate-700 pb-3 mb-6">Received Messages</h3>

            <?php if (empty($messages)): ?>
                <p class="text-slate-400 text-sm py-12 text-center">No contact inquiries found in the database.</p>
            <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($messages as $msg): ?>
                        <div class="border border-slate-700 bg-slate-900 rounded-xl p-5 flex flex-col md:flex-row justify-between items-start gap-4 hover:border-slate-600 transition-all">
                            <div class="space-y-2 flex-1">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <h4 class="font-bold text-white text-lg"><?php echo htmlspecialchars($msg['name']); ?></h4>
                                    <span class="text-xs text-indigo-400 font-medium bg-indigo-500/10 border border-indigo-500/20 px-2.5 py-0.5 rounded-full">
                                        <?php echo htmlspecialchars($msg['email']); ?>
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 font-medium">
                                    Received: <?php echo date('M d, Y \a\t g:i A', strtotime($msg['created_at'])); ?>
                                </p>
                                <p class="text-sm text-slate-300 leading-relaxed pt-1 whitespace-pre-line">
                                    <?php echo htmlspecialchars($msg['message']); ?>
                                </p>
                            </div>
                            
                            <div class="flex md:flex-col gap-2 w-full md:w-auto border-t md:border-t-0 border-slate-800 pt-4 md:pt-0 justify-end shrink-0">
                                <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" 
                                   class="flex-1 md:w-28 text-center bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold py-2 px-3 rounded-lg transition-colors">
                                    Reply Email
                                </a>
                                <a href="view-messages.php?delete=<?php echo $msg['id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this message?');" 
                                   class="flex-1 md:w-28 text-center bg-rose-600/10 hover:bg-rose-600 text-rose-400 hover:text-white text-xs font-bold py-2 px-3 rounded-lg transition-colors border border-rose-500/20">
                                    Delete
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