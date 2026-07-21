<?php
// submit-testimony.php
require_once 'config/db.php';
$current_page = 'testimony';

$success = false;
$error = '';

// Define the administrator's email notification endpoint here
$admin_email = 'admin@lifechangersministry.org'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Processing validation guards using clean "or" strings to avoid parsing conflicts
    if (empty($name) or empty($title) or empty($message)) {
        $error = 'Please populate all structural input spaces before submitting your praise.';
    } else {
        try {
            // PHASE 1: LOG INTO DATABASE FOR TERMINAL ACCESSIBILITY
            $stmt = $pdo->prepare("INSERT INTO testimonies (name, title, message, status, created_at) VALUES (?, ?, ?, 'pending', NOW())");
            $stmt->execute([$name, $title, $message]);
            
            // PHASE 2: CONSTRUCT AND DISPATCH INBOX EMAIL BROADCAST
            $email_subject = "New Testimony Submission Pending Review: " . $title;
            
            // Format a clean, human-readable email layout template
            $email_body = "Greetings Admin,\n\n";
            $email_body .= "A new testimony submission has been received from the public portal and is currently pending verification.\n\n";
            $email_body .= "--------------------------------------------------\n";
            $email_body .= "Contributor Name: " . $name . "\n";
            $email_body .= "Testimony Title:  " . $title . "\n";
            $email_body .= "Submission Date:  " . date('Y-m-d H:i:s') . "\n";
            $email_body .= "--------------------------------------------------\n\n";
            $email_body .= "Message Body:\n" . $message . "\n\n";
            $email_body .= "To approve or delete this entry, please log in to your LCM Control Terminal dashboard.";

            // Construct native email protocol headers
            $email_headers = "From: system@lifechangersministry.org\r\n";
            $email_headers .= "Reply-To: system@lifechangersministry.org\r\n";
            $email_headers .= "X-Mailer: PHP/" . phpversion();

            // Fire off the email broadcast payload
            @mail($admin_email, $email_subject, $email_body, $email_headers);

            $success = true;
            // Flush form data values upon complete execution success
            $name = $title = $message = '';

        } catch (PDOException $e) {
            if (in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']) or $_SERVER['SERVER_NAME'] === 'localhost') {
                $error = 'System submission failed: ' . $e->getMessage();
            } else {
                $error = 'Your testimony submission could not be captured. Please try again shortly.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Testimony - Life Changers Ministry</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased flex flex-col min-h-screen">

    <?php include_once 'includes/navbar.php'; ?>

    <main class="flex-grow max-w-md w-full mx-auto px-4 py-12">
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs">
            <h2 class="text-xl font-bold text-slate-900 mb-2 tracking-tight">Share Your Testimony</h2>
            <p class="text-xs text-slate-500 mb-6">Has God done something remarkable in your life? Share your praise report with our faith community.</p>

            <?php if ($success): ?>
                <div class="mb-6 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-xs text-emerald-700 font-semibold">
                    ✓ Praise submitted! Your testimony has been logged and sent to the administrator for publication review.
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-xs text-red-700 font-medium whitespace-pre-line">
                    ⚠ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="submit-testimony.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Your Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Testimony Title</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($title ?? ''); ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="e.g., Miraculous Healing, Provision">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Your Story</label>
                    <textarea name="message" rows="5" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500" placeholder="Describe what the Lord has done..."></textarea>
                </div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm py-2.5 rounded-lg shadow-xs transition-colors">
                    Submit Praise Report
                </button>
            </form>
        </div>
    </main>

    <?php include_once 'includes/footer.php'; ?>
</body>
</html>