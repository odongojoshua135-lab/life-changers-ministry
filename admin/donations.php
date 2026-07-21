<?php
// admin/donations.php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Sidebar & Header Configuration Variables
$admin_page = 'donations';
$page_title = 'Donation & Giving Settings';

$success = '';
$error = '';

// --- Handle Form Updates ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_donations'])) {
    $momo_number       = trim($_POST['momo_number']);
    $momo_name         = trim($_POST['momo_name']);
    $bank_name         = trim($_POST['bank_name']);
    $bank_account      = trim($_POST['bank_account']);
    $bank_holder       = trim($_POST['bank_holder']);
    $custom_giving_url = trim($_POST['custom_giving_url']);
    $instruction_text  = trim($_POST['instruction_text']);

    $stmt = $pdo->prepare("UPDATE donation_settings SET 
        momo_number = :momo_number, 
        momo_name = :momo_name, 
        bank_name = :bank_name, 
        bank_account = :bank_account, 
        bank_holder = :bank_holder, 
        custom_giving_url = :custom_giving_url,
        instruction_text = :instruction_text 
        WHERE id = 1");
        
    $stmt->execute([
        'momo_number'       => $momo_number,
        'momo_name'         => $momo_name,
        'bank_name'         => $bank_name,
        'bank_account'      => $bank_account,
        'bank_holder'       => $bank_holder,
        'custom_giving_url' => $custom_giving_url,
        'instruction_text'  => $instruction_text
    ]);
    
    $success = 'Ministry contribution portal configurations saved.';
}

// Fetch current configurations
$donation = $pdo->query("SELECT * FROM donation_settings WHERE id = 1")->fetch();
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Settings - Life Changers Admin</title>
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

    <!-- MAIN CONTENT CONTAINER -->
    <div class="max-w-4xl mx-auto w-full">
        <header class="mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight text-white">Configure Donation Options</h1>
            <p class="text-slate-400 text-sm mt-1">Manage public Mobile Money numbers, bank coordinates, and tithe/giving program instructional statements.</p>
        </header>

        <?php if (!empty($success)): ?>
            <div class="bg-emerald-950/60 border border-emerald-500/30 text-emerald-300 p-4 rounded-xl mb-6 text-xs font-semibold">✓ <?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="bg-[#121a35] border border-slate-800 p-6 rounded-2xl shadow-xl">
            <form action="donations.php" method="POST" class="space-y-6">
                
                <!-- Mobile Money Credentials -->
                <div class="border-b border-slate-800 pb-5">
                    <h3 class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-3">1. Mobile Money Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Momo Phone Number</label>
                            <input type="text" name="momo_number" value="<?php echo htmlspecialchars($donation['momo_number'] ?? ''); ?>" placeholder="e.g., +256 789 123456"
                                   class="w-full bg-[#0b1329] border border-slate-800 rounded-lg px-3 py-2 text-white text-xs focus:outline-none focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Registered Account Name</label>
                            <input type="text" name="momo_name" value="<?php echo htmlspecialchars($donation['momo_name'] ?? ''); ?>" placeholder="e.g., Life Changers Ministry"
                                   class="w-full bg-[#0b1329] border border-slate-800 rounded-lg px-3 py-2 text-white text-xs focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Bank Transfers Credentials -->
                <div class="border-b border-slate-800 pb-5">
                    <h3 class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-3">2. Bank Account Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Bank Name</label>
                            <input type="text" name="bank_name" value="<?php echo htmlspecialchars($donation['bank_name'] ?? ''); ?>" placeholder="e.g., Stanbic Bank"
                                   class="w-full bg-[#0b1329] border border-slate-800 rounded-lg px-3 py-2 text-white text-xs focus:outline-none focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Account Number</label>
                            <input type="text" name="bank_account" value="<?php echo htmlspecialchars($donation['bank_account'] ?? ''); ?>" placeholder="e.g., 903000..."
                                   class="w-full bg-[#0b1329] border border-slate-800 rounded-lg px-3 py-2 text-white text-xs focus:outline-none focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-300 mb-1">Account Holder Name</label>
                            <input type="text" name="bank_holder" value="<?php echo htmlspecialchars($donation['bank_holder'] ?? ''); ?>" placeholder="e.g., Life Changers"
                                   class="w-full bg-[#0b1329] border border-slate-800 rounded-lg px-3 py-2 text-white text-xs focus:outline-none focus:border-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Custom / Online Integration Link -->
                <div class="border-b border-slate-800 pb-5">
                    <h3 class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-3">3. Online Giving Link (Optional)</h3>
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">External Payment Gateway URL</label>
                        <input type="url" name="custom_giving_url" value="<?php echo htmlspecialchars($donation['custom_giving_url'] ?? ''); ?>" placeholder="https://flutterwave.com/pay/lifechangers"
                               class="w-full bg-[#0b1329] border border-slate-800 rounded-lg px-3 py-2 text-white text-xs focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <!-- Note / Custom Instructions Message -->
                <div>
                    <h3 class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-2">4. Instruction Text Note</h3>
                    <label class="block text-xs text-slate-400 mb-2">Provide a short note encouraging or guiding the congregation on how their offerings support local work.</label>
                    <textarea name="instruction_text" rows="3"
                              class="w-full bg-[#0b1329] border border-slate-800 rounded-lg px-3 py-2 text-white text-xs focus:outline-none focus:border-indigo-500"><?php echo htmlspecialchars($donation['instruction_text'] ?? ''); ?></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" name="update_donations" 
                            class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold py-2.5 px-6 rounded-lg transition-colors cursor-pointer shadow-md">
                        Update Contribution Information
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- CLOSE WRAPPER TAGS OPENED BY ADMIN-SIDEBAR.PHP -->
    </main>
</div>

</body>
</html>