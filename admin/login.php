<?php
// admin/login.php
session_start();
require_once '../config/db.php';

// If the admin is already logged in, redirect them straight to the dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        // Fetch admin details based on username
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $admin = $stmt->fetch();

        // Verify password hash
        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Regulate session hijacking risks by regenerating session ID on login
            session_regenerate_id(true);
            
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            
            header("Location: index.php");
            exit;
        } else {
            $error = 'Invalid username or password configuration.';
        }
    } else {
        $error = 'Please fill in all credential fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Life Changers Ministry</title>
    <!-- Tailwind CSS CDN for sleek styling -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-slate-800 p-8 rounded-xl shadow-2xl border border-slate-700">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-white tracking-wide">Ministry Admin Portal</h2>
            <p class="text-slate-400 text-sm mt-1">Life Changers for Christ Ministry</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-500/10 border border-red-500 text-red-400 p-3 rounded-lg text-sm mb-6">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="space-y-5">
            <div>
                <label for="username" class="block text-sm font-medium text-slate-300 mb-1">Username</label>
                <input type="text" name="username" id="username" required 
                       class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-indigo-500 transition-colors">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-300 mb-1">Password</label>
                <input type="password" name="password" id="password" required 
                       class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-indigo-500 transition-colors">
            </div>

            <button type="submit" 
                    class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2.5 rounded-lg transition-colors mt-4 cursor-pointer shadow-lg shadow-indigo-600/20">
                Sign In
            </button>
        </form>
    </div>

</body>
</html>