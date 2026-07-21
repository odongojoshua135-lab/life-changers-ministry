<?php
// admin/messages.php
require_once __DIR__ . '/../config/db.php';

// Set active navigation page and header title
$admin_page = 'messages';
$page_title = 'Contact Messages & Inquiries';

$message = '';
$error = '';

// -------------------------------------------------------------------
// 1. HANDLE DELETE MESSAGE
// -------------------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->execute([$delete_id]);
        header("Location: messages.php?msg=deleted");
        exit;
    } catch (PDOException $e) {
        $error = "Error deleting message: " . $e->getMessage();
    }
}

// -------------------------------------------------------------------
// 2. HANDLE MARK AS READ / UNREAD STATUS
// -------------------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] === 'toggle_status' && isset($_GET['id'])) {
    $msg_id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("UPDATE messages SET status = IF(status = 'read', 'unread', 'read') WHERE id = ?");
        $stmt->execute([$msg_id]);
        header("Location: messages.php");
        exit;
    } catch (PDOException $e) {
        // Silently catch if status column isn't created yet
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $message = "Message deleted successfully!";
}

// -------------------------------------------------------------------
// 3. FETCH ALL MESSAGES
// -------------------------------------------------------------------
try {
    $stmt = $pdo->query("SELECT * FROM messages ORDER BY id DESC");
    $allMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to fetch messages: " . $e->getMessage();
    $allMessages = [];
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Admin Console</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0b1329] text-slate-200 font-sans antialiased min-h-screen flex">

    <!-- INCLUDE SHARED SIDEBAR -->
    <?php 
    $sidebar_path = __DIR__ . '/includes/admin-sidebar.php';
    if (file_exists($sidebar_path)) {
        include_once $sidebar_path;
    } else {
        // Fallback if admin-sidebar.php is sitting in the admin root folder directly
        include_once __DIR__ . '/admin-sidebar.php';
    }
    ?>

    <!-- MAIN PAGE CONTENT -->
    <div class="max-w-6xl mx-auto w-full">

        <!-- HEADER SUBTITLE -->
        <div class="mb-6">
            <p class="text-xs text-slate-400">Manage public user submissions and directly respond via email.</p>
        </div>

        <!-- ALERTS -->
        <?php if (!empty($message)): ?>
            <div class="mb-6 p-4 bg-emerald-950/60 border border-emerald-500/30 text-emerald-300 text-xs font-semibold rounded-xl">
                ✓ <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="mb-6 p-4 bg-rose-950/60 border border-rose-500/30 text-rose-300 text-xs font-semibold rounded-xl">
                ⚠ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- MESSAGES PANEL -->
        <div class="bg-[#121a35] border border-slate-800/90 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-800">
                <h3 class="text-sm font-bold text-indigo-400 uppercase tracking-wider">Inbox (<?php echo count($allMessages); ?>)</h3>
            </div>

            <?php if (empty($allMessages)): ?>
                <div class="py-12 text-center text-slate-500 text-xs">
                    No contact messages found in the database.
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-slate-800 text-slate-400 uppercase font-bold text-[11px] tracking-wider">
                                <th class="pb-3 px-4">Sender</th>
                                <th class="pb-3 px-4">Message</th>
                                <th class="pb-3 px-4">Date</th>
                                <th class="pb-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            <?php foreach ($allMessages as $msg): ?>
                                <?php 
                                    $id    = $msg['id'];
                                    $name  = $msg['name'] ?? $msg['full_name'] ?? 'Unknown';
                                    $email = $msg['email'] ?? $msg['email_address'] ?? '';
                                    $body  = $msg['message'] ?? '';
                                    $date  = $msg['created_at'] ?? $msg['date'] ?? null;
                                ?>
                                <tr class="hover:bg-slate-800/30 transition-colors">
                                    <td class="py-4 px-4 align-top w-1/4">
                                        <div class="font-bold text-white text-sm">
                                            <?php echo htmlspecialchars($name); ?>
                                        </div>
                                        <div class="text-indigo-400 text-xs mt-0.5">
                                            <?php echo htmlspecialchars($email); ?>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 align-top">
                                        <p class="text-slate-300 leading-relaxed whitespace-pre-line text-xs">
                                            <?php echo htmlspecialchars($body); ?>
                                        </p>
                                    </td>
                                    <td class="py-4 px-4 align-top whitespace-nowrap text-slate-400">
                                        <?php echo $date ? date('M d, Y · g:i A', strtotime($date)) : 'N/A'; ?>
                                    </td>
                                    <td class="py-4 px-4 align-top text-right whitespace-nowrap">
                                        <!-- REPLY LINK -->
                                        <a href="mailto:<?php echo rawurlencode($email); ?>?subject=<?php echo rawurlencode('Re: Inquiry to Life Changers Ministry'); ?>" 
                                           class="inline-flex items-center gap-1 bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-3 py-1.5 rounded-lg text-xs transition-colors mr-2">
                                            ✉ Reply
                                        </a>

                                        <!-- DELETE -->
                                        <a href="messages.php?action=delete&id=<?php echo $id; ?>" 
                                           onclick="return confirm('Are you sure you want to delete this message?');"
                                           class="inline-block bg-rose-950/60 border border-rose-800/50 hover:bg-rose-900/80 text-rose-300 font-bold px-3 py-1.5 rounded-lg text-xs transition-colors">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- CLOSE WRAPPER TAGS OPENED BY ADMIN-SIDEBAR.PHP -->
    </main>
</div>

</body>
</html>