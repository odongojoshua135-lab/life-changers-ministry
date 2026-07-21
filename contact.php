<?php
// contact.php
require_once 'config/db.php';
$current_page = 'contact';

$success = false;
$error = '';

// Define administrator notification email
$admin_email = 'admin@lifechangersministry.org'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check both potential form field names (name / full_name, email / email_address)
    $name    = trim($_POST['name'] ?? $_POST['full_name'] ?? '');
    $email   = trim($_POST['email'] ?? $_POST['email_address'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Please fill out all required field blocks before submitting.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please supply a structurally valid email address.';
    } else {
        try {
            // Force PDO to throw exceptions for debugging
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 1. SAVE TO DATABASE FOR ADMIN DASHBOARD
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, message, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$name, $email, $message]);

            // 2. DISPATCH EMAIL NOTIFICATION TO ADMIN
            $email_subject = "New Contact Inquiry from: " . $name;
            
            $email_body = "Hello Admin,\n\n";
            $email_body .= "You have received a new contact inquiry via the website.\n\n";
            $email_body .= "--------------------------------------------------\n";
            $email_body .= "Sender Name:  " . $name . "\n";
            $email_body .= "Sender Email: " . $email . "\n";
            $email_body .= "Date Received: " . date('Y-m-d H:i:s') . "\n";
            $email_body .= "--------------------------------------------------\n\n";
            $email_body .= "Message Content:\n" . $message . "\n\n";
            $email_body .= "Log in to your admin console to manage your inbox messages.";

            $email_headers = "From: system@lifechangersministry.org\r\n";
            $email_headers .= "Reply-To: " . $email . "\r\n";
            $email_headers .= "X-Mailer: PHP/" . phpversion();

            // Suppress mail error on local dev server where SMTP might not be configured
            @mail($admin_email, $email_subject, $email_body, $email_headers);

            $success = true;
            // Clear inputs on success
            $name = $email = $message = ''; 
        } catch (PDOException $e) {
            if (in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1']) || ($_SERVER['SERVER_NAME'] ?? '') === 'localhost') {
                $error = 'Database insertion failed: ' . $e->getMessage();
            } else {
                $error = 'Database processing failed. Please try again shortly.';
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
    <title>Contact Us - Life Changers Ministry</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased flex flex-col min-h-screen">

    <?php include_once 'includes/navbar.php'; ?>

    <!-- CONTENT FIELD LAYOUT -->
    <main class="flex-grow max-w-md w-full mx-auto px-4 py-12">
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-xs">
            <h2 class="text-xl font-bold text-slate-900 mb-2 tracking-tight">Get In Touch</h2>
            <p class="text-xs text-slate-500 mb-6">Have inquiries about counseling, partnerships, or fellowships? Send us a message.</p>

            <?php if ($success): ?>
                <div class="mb-6 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-xs text-emerald-700 font-semibold">
                    ✓ Thank you! Your message has been sent successfully. We will get back to you shortly.
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-xs text-red-700 font-medium whitespace-pre-line">
                    ⚠ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="contact.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Message</label>
                    <textarea name="message" rows="4" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-indigo-500"><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                </div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm py-2.5 rounded-lg shadow-xs transition-colors">
                    Send Message
                </button>
            </form>
        </div>
    </main>

    <?php include_once 'includes/footer.php'; ?>
</body>
</html>