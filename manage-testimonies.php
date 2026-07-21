<?php
// manage-testimonies.php
require_once 'config/db.php';

$admin_page = 'testimonies';
$page_title = 'Review Praise Submissions';

$success_msg = '';
$error_msg = '';

// 1. OPERATION ACTIONS PROCESSOR HANDLERS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);

    if ($id > 0) {
        try {
            if ($action === 'approve') {
                $stmt = $pdo->prepare("UPDATE testimonies SET status = 'approved' WHERE id = ?");
                $stmt->execute([$id]);
                $success_msg = 'Testimony status upgraded to Approved successfully.';
            } elseif ($action === 'delete') {
                $stmt = $pdo->prepare("DELETE FROM testimonies WHERE id = ?");
                $stmt->execute([$id]);
                $success_msg = 'Selected testimony statement deleted permanently from historical records.';
            }
        } catch (PDOException $e) {
            $error_msg = 'Action execution failure: ' . $e->getMessage();
        }
    }
}

// 2. QUERY RAW RECORD RECORDS MATRIX
try {
    // Queries all testimonies, putting pending ones first, sorted by newest submission date
    $stmt = $pdo->query("SELECT * FROM testimonies ORDER BY status DESC, created_at DESC");
    $testimonies = $stmt->fetchAll();
} catch (PDOException $e) {
    $testimonies = [];
    $error_msg = 'Failed to fetch database data: ' . $e->getMessage();
}

// 3. INJECT THE STANDARDIZED BLOCKS
require_once 'includes/admin-header.php';
require_once 'includes/admin-sidebar.php';
?>

<!-- ACTION MESSAGE BANNERS -->
<?php if (!empty($success_msg)): ?>
    <div class="mb-6 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-xs text-emerald-700 font-semibold shadow-xs">
        ✓ <?php echo htmlspecialchars($success_msg); ?>
    </div>
<?php endif; ?>

<?php if (!empty($error_msg)): ?>
    <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-xs text-red-700 font-medium shadow-xs">
        ⚠ <?php echo htmlspecialchars($error_msg); ?>
    </div>
<?php endif; ?>

<!-- MAIN DATA MATRIX DISPLAY FRAME -->
<div class="bg-white border border-slate-200 rounded-xl shadow-2xs overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold text-slate-900">Submitted Praises</h3>
            <p class="text-xs text-slate-400">Review, approve for publication, or remove public platform entries.</p>
        </div>
        <span class="bg-indigo-50 text-indigo-700 font-bold text-[11px] px-2.5 py-1 rounded-full border border-indigo-100 shadow-2xs">
            Total Ledger count: <?php echo count($testimonies); ?>
        </span>
    </div>

    <?php if (empty($testimonies)): ?>
        <div class="p-12 text-center">
            <span class="text-3xl block mb-2">📥</span>
            <p class="text-xs text-slate-400 font-medium">No testimony records located in your database.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="p-4 w-40">Contributor</th>
                        <th class="p-4 w-48">Title Headline</th>
                        <th class="p-4">Message Context</th>
                        <th class="p-4 w-28">Status State</th>
                        <th class="p-4 w-44 text-right">Operational Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-600">
                    <?php foreach ($testimonies as $row): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 align-top font-bold text-slate-900">
                                <?php echo htmlspecialchars($row['name']); ?>
                                <span class="block font-normal text-[10px] text-slate-400 mt-0.5">
                                    <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                                </span >
                            </td>
                            <td class="p-4 align-top font-semibold text-slate-700">
                                <?php echo htmlspecialchars($row['title']); ?>
                            </td>
                            <td class="p-4 align-top leading-relaxed whitespace-pre-line text-slate-500 max-w-sm">
                                <?php echo htmlspecialchars($row['message']); ?>
                            </td>
                            <td class="p-4 align-top">
                                <?php if ($row['status'] === 'approved'): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 shadow-3xs">
                                        Published
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100 shadow-3xs">
                                        Pending Review
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 align-top text-right space-y-2">
                                <div class="flex items-center justify-end space-x-2">
                                    <?php if ($row['status'] !== 'approved'): ?>
                                        <form action="manage-testimonies.php" method="POST" class="inline">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white font-bold text-[11px] px-2.5 py-1.5 rounded-md border border-indigo-100 hover:border-indigo-600 shadow-3xs transition-all">
                                                Approve Entry
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <form action="manage-testimonies.php" method="POST" class="inline">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <!-- Uses the JS listener from main.js to prompt confirmation -->
                                        <button type="submit" data-confirm="Are you sure you want to permanently delete this testimony statement?" class="bg-slate-50 hover:bg-red-50 text-slate-400 hover:text-red-700 font-bold text-[11px] px-2.5 py-1.5 rounded-md border border-slate-200 hover:border-red-200 shadow-3xs transition-all">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php 
require_once 'includes/admin-footer.php'; 
?>