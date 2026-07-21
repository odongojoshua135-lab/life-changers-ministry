<?php
// includes/admin-header.php
if (!isset($admin_page)) {
    $admin_page = '';
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Admin Panel'; ?> - Life Changers</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Global Custom CSS Styles -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="h-full font-sans antialiased text-slate-800 flex overflow-hidden">