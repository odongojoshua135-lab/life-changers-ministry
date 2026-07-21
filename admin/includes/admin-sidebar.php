<?php
// admin/includes/admin-sidebar.php

/**
 * Returns Tailwind CSS classes for navigation links based on active page
 */
if (!function_exists('admin_nav_class')) {
    function admin_nav_class($active_name, $current_active) {
        return ($current_active === $active_name)
            ? 'flex items-center space-x-3 bg-indigo-950/80 border border-indigo-500/30 text-indigo-300 px-4 py-2.5 rounded-xl text-xs font-semibold shadow-xs transition-all'
            : 'flex items-center space-x-3 text-slate-400 hover:bg-slate-800/50 hover:text-white px-4 py-2.5 rounded-xl text-xs font-medium transition-colors';
    }
}
?>
<!-- ADMIN CONTROL SIDEBAR -->
<aside class="w-64 bg-[#080d1e] border-r border-slate-800/80 flex flex-col h-full flex-shrink-0 min-h-screen">
    <!-- Brand / Title Identity -->
    <div class="h-16 flex items-center px-6 border-b border-slate-800/80">
        <a href="index.php" class="text-base font-black text-indigo-400 tracking-tight flex items-center space-x-2">
            <span>⚡</span>
            <span>Admin Console</span>
        </a>
    </div>

    <!-- Navigation Menu Matrix -->
    <nav class="flex-grow p-4 space-y-1 overflow-y-auto">
        <a href="index.php" class="<?php echo admin_nav_class('dashboard', $admin_page ?? ''); ?>">
            <span>📊</span> <span>Dashboard Hub</span>
        </a>
        <a href="gallery.php" class="<?php echo admin_nav_class('gallery', $admin_page ?? ''); ?>">
            <span>🖼️</span> <span>Manage Gallery</span>
        </a>
        <a href="events.php" class="<?php echo admin_nav_class('events', $admin_page ?? ''); ?>">
            <span>📅</span> <span>Manage Events</span>
        </a>
        <a href="blogs.php" class="<?php echo admin_nav_class('blogs', $admin_page ?? ''); ?>">
            <span>📝</span> <span>Manage Blogs</span>
        </a>
        <a href="testimonies.php" class="<?php echo admin_nav_class('testimonies', $admin_page ?? ''); ?>">
            <span>🙏</span> <span>Testimonies</span>
        </a>
        <a href="team.php" class="<?php echo admin_nav_class('team', $admin_page ?? ''); ?>">
            <span>👥</span> <span>Manage Team</span>
        </a>
        <a href="homepage.php" class="<?php echo admin_nav_class('homepage', $admin_page ?? ''); ?>">
            <span>🏠</span> <span>Manage Homepage</span>
        </a>
        <a href="donations.php" class="<?php echo admin_nav_class('donations', $admin_page ?? ''); ?>">
            <span>💳</span> <span>Donation Settings</span>
        </a>

        <!-- CONTACT MESSAGES LINK -->
        <a href="messages.php" class="<?php echo admin_nav_class('messages', $admin_page ?? ''); ?>">
            <span>📨</span> <span>Contact Messages</span>
        </a>
        
        <div class="pt-4 mt-4 border-t border-slate-800/80">
            <span class="px-4 text-[10px] font-bold uppercase text-slate-500 tracking-wider block mb-2">System Tools</span>
            <a href="../index.php" target="_blank" class="flex items-center space-x-3 text-slate-400 hover:text-white px-4 py-2 text-xs font-medium transition-colors">
                <span>🌐</span> <span>View Live Website</span>
            </a>
        </div>
    </nav>

    <!-- Admin Context Profile / Logout -->
    <div class="p-4 border-t border-slate-800/80 bg-[#060a17]">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-indigo-600/30 border border-indigo-500/40 text-indigo-300 flex items-center justify-center font-bold text-xs uppercase">
                    AD
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-xs font-bold text-slate-200 truncate">Administrator</span>
                    <span class="text-[10px] text-slate-500 truncate">System Terminal</span>
                </div>
            </div>
            <a href="logout.php" title="Sign Out" class="text-slate-400 hover:text-rose-400 transition-colors p-1.5 rounded-lg hover:bg-slate-800/60">
                🚪
            </a>
        </div>
    </div>
</aside>

<!-- OPENING WORKSPACE PANEL WRAPPER -->
<div class="flex-grow flex flex-col h-full overflow-hidden bg-[#0b1329]">
    <!-- Sub-Header Workspace Bar -->
    <header class="h-16 bg-[#080d1e] border-b border-slate-800/80 flex items-center px-8 justify-between flex-shrink-0">
        <h2 class="text-sm font-bold text-white tracking-tight">
            <?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Control Workspace'; ?>
        </h2>
        <div class="text-xs font-medium text-slate-400">
            System Date: <span class="text-indigo-400 font-semibold"><?php echo date('F d, Y'); ?></span>
        </div>
    </header>
    
    <!-- MAIN SCROLLABLE CONTENT AREA START -->
    <main class="flex-grow p-8 overflow-y-auto bg-[#0b1329]">