<?php
// includes/navbar.php
// Set default if not specified in the parent script
if (!isset($current_page)) {
    $current_page = '';
}

// Helper function to handle active page typography highlights
function nav_class($page_name, $current_page) {
    return ($current_page === $page_name) 
        ? 'text-indigo-600 font-bold border-b-2 border-indigo-600 pb-1 transition-colors' 
        : 'hover:text-indigo-600 transition-colors';
}
?>
<!-- GLOBAL NAVIGATION BAR -->
<nav class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex-shrink-0">
                <a href="index.php" class="text-xl font-extrabold tracking-tight text-indigo-600">Life Changers Ministry</a>
            </div>
            <div class="hidden md:flex space-x-8 text-sm font-semibold text-slate-600 items-center">
                <a href="index.php" class="<?php echo nav_class('home', $current_page); ?>">Home</a>
                <a href="about.php" class="<?php echo nav_class('about', $current_page); ?>">About Us</a>
                <a href="activities.php" class="<?php echo nav_class('activities', $current_page); ?>">Activities</a>
                <a href="gallery.php" class="<?php echo nav_class('gallery', $current_page); ?>">Gallery</a>
                <a href="events.php" class="<?php echo nav_class('events', $current_page); ?>">Events</a>
                <a href="blog.php" class="<?php echo nav_class('blog', $current_page); ?>">Blogs</a>
                <a href="contact.php" class="<?php echo nav_class('contact', $current_page); ?>">Contact</a>
            </div>
            <div>
                <a href="donate.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-full text-sm font-bold tracking-wide shadow-sm transition-all hover:shadow-md">Partner / Donate</a>
            </div>
        </div>
    </div>
</nav>