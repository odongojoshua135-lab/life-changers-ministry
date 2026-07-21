<?php
// index.php
require_once 'config/db.php';

// Active navigation identifier
$current_page = 'home';

// 1. Fetch Homepage Settings (Hero, Welcome, About)
$homeStmt = $pdo->query("SELECT * FROM homepage_settings WHERE id = 1");
$homeSettings = $homeStmt->fetch() ?: []; // Ensure array type even if query returns false

// 2. Fetch Top 3 Upcoming Events
$eventsStmt = $pdo->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 3");
$upcomingEvents = $eventsStmt->fetchAll();

// 3. Fetch Team Members
$teamStmt = $pdo->query("SELECT * FROM team ORDER BY id ASC");
$teamMembers = $teamStmt->fetchAll();

// 4. Fetch Approved Testimonies
$testiStmt = $pdo->query("SELECT * FROM testimonies WHERE status = 'approved' ORDER BY id DESC LIMIT 3");
$testimonies = $testiStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Life Changers Ministry - Transforming Lives, Restoring Hope</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased flex flex-col min-h-screen">

    <!-- GLOBAL DYNAMIC NAVIGATION BAR -->
    <?php 
    if (file_exists('includes/navbar.php')) {
        include_once 'includes/navbar.php';
    } else {
    ?>
        <nav class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex-shrink-0">
                        <a href="index.php" class="text-xl font-extrabold tracking-tight text-indigo-600">Life Changers Ministry</a>
                    </div>
                    <div class="hidden md:flex space-x-8 text-sm font-semibold text-slate-600">
                        <a href="index.php" class="text-indigo-600 hover:text-indigo-700 transition-colors">Home</a>
                        <a href="about.php" class="hover:text-indigo-600 transition-colors">About Us</a>
                        <a href="activities.php" class="hover:text-indigo-600 transition-colors">Activities</a>
                        <a href="gallery.php" class="hover:text-indigo-600 transition-colors">Gallery</a>
                        <a href="events.php" class="hover:text-indigo-600 transition-colors">Events</a>
                        <a href="blog.php" class="hover:text-indigo-600 transition-colors">Blogs</a>
                        <a href="contact.php" class="hover:text-indigo-600 transition-colors">Contact</a>
                    </div>
                    <div>
                        <a href="donate.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-full text-sm font-bold tracking-wide shadow-sm transition-all hover:shadow-md">Partner / Donate</a>
                    </div>
                </div>
            </div>
        </nav>
    <?php } ?>

    <!-- 1. DYNAMIC HERO SECTION -->
    <section class="relative bg-slate-900 text-white overflow-hidden py-24 sm:py-32 flex items-center justify-center">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#4f46e5_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="relative max-w-4xl mx-auto text-center px-4">
            <h1 class="text-4xl sm:text-6xl font-black tracking-tight leading-none mb-6">
                <?php echo htmlspecialchars($homeSettings['hero_title'] ?? 'Transforming Lives, Restoring Hope'); ?>
            </h1>
            <p class="text-lg sm:text-xl text-slate-300 max-w-2xl mx-auto mb-10 font-normal leading-relaxed">
                <?php echo htmlspecialchars($homeSettings['hero_subtitle'] ?? 'Welcome to Life Changers Ministry. Join us in making a global impact.'); ?>
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="about.php" class="bg-indigo-600 hover:bg-indigo-500 text-white px-8 py-3 rounded-lg font-bold shadow-md transition-all">Learn More</a>
                <a href="events.php" class="bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 px-8 py-3 rounded-lg font-bold transition-all">Upcoming Events</a>
            </div>
        </div>
    </section>

    <!-- 2. DYNAMIC WELCOME BANNER CALLOUT -->
    <?php if(!empty($homeSettings['welcome_message'])): ?>
    <section class="bg-indigo-50 border-y border-indigo-100 py-12">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <span class="text-xs uppercase tracking-widest font-bold text-indigo-600">A Word From Leadership</span>
            <p class="text-xl sm:text-2xl font-serif text-slate-700 italic mt-3 max-w-3xl mx-auto">
                "<?php echo htmlspecialchars($homeSettings['welcome_message']); ?>"
            </p>
        </div>
    </section>
    <?php endif; ?>

    <!-- 3. DYNAMIC ABOUT US MATRIX -->
    <section id="about" class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="text-xs font-bold uppercase text-indigo-600 tracking-wider">Our Core Vision</span>
                <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 mt-2 mb-6">Who We Are & What We Believe</h2>
                <div class="text-slate-600 leading-relaxed text-base space-y-4">
                    <p><?php echo nl2br(htmlspecialchars($homeSettings['about_summary'] ?? 'Life Changers Ministry began with a vision to serve families and restore faith through action.')); ?></p>
                </div>
            </div>
            <div class="bg-gradient-to-tr from-indigo-600 to-violet-700 rounded-2xl aspect-video shadow-xl flex items-center justify-center p-8 text-white text-center relative overflow-hidden group">
                <div class="absolute inset-0 bg-slate-950 opacity-20 mix-blend-multiply"></div>
                <div class="relative z-10">
                    <h3 class="text-2xl font-bold mb-2">Join Our Weekly Services</h3>
                    <p class="text-indigo-200 text-sm max-w-xs mx-auto">Experience fellowship, uplifting worship, and powerful message transformations every Sunday.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. DYNAMIC UPCOMING EVENTS GRID PANEL -->
    <section class="bg-slate-100 py-20 border-y border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <span class="text-xs font-bold uppercase text-indigo-600 tracking-wider">Get Involved</span>
                    <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 mt-1">Upcoming Ministry Events</h2>
                </div>
                <a href="events.php" class="text-sm font-bold text-indigo-600 hover:text-indigo-700 transition-colors hidden sm:block">View Full Calendar &rarr;</a>
            </div>

            <?php if(empty($upcomingEvents)): ?>
                <p class="text-slate-500 text-sm py-8 text-center bg-white rounded-xl border border-slate-200 shadow-sm">No new upcoming community outreach events lined up right now.</p>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <?php foreach($upcomingEvents as $evt): ?>
                        <?php 
                            // SAFE ARRAY KEY RESOLUTION TO PREVENT UNDEFINED KEY WARNINGS
                            $rawTime = $evt['event_time'] ?? $evt['time'] ?? $evt['start_time'] ?? null;
                            $formattedTime = !empty($rawTime) ? date('g:i A', strtotime($rawTime)) : 'TBA';
                        ?>
                        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col justify-between">
                            <div class="p-6">
                                <span class="inline-block px-2.5 py-1 bg-indigo-50 border border-indigo-100 text-indigo-600 rounded-md text-xs font-bold mb-4 shadow-2xs">
                                    <?php echo date('M d, Y', strtotime($evt['event_date'])); ?>
                                </span>
                                <h3 class="font-bold text-lg text-slate-900 mb-2 leading-snug"><?php echo htmlspecialchars($evt['title']); ?></h3>
                                <p class="text-sm text-slate-600 line-clamp-3 mb-4"><?php echo htmlspecialchars($evt['description']); ?></p>
                            </div>
                            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                                <span>⏰ <?php echo $formattedTime; ?></span>
                                <span class="font-medium">📍 <?php echo htmlspecialchars($evt['location'] ?? 'Church Main Sanctuary'); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- 5. DYNAMIC LEADERSHIP ROSTER SECTION -->
    <section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-xs font-bold uppercase text-indigo-600 tracking-wider">Our Shepherds</span>
            <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 mt-1">Ministry Leadership Team</h2>
            <p class="text-slate-500 text-sm mt-2">Meet the dedicated team, pastors, and operational leaders heading the operations framework at Life Changers.</p>
        </div>

        <?php if(empty($teamMembers)): ?>
            <p class="text-slate-400 text-sm text-center py-6">Roster metrics are currently being configured.</p>
        <?php else: ?>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-8">
                <?php foreach($teamMembers as $mbr): ?>
                    <div class="text-center group">
                        <div class="w-32 h-32 mx-auto rounded-full overflow-hidden border-2 border-slate-200 group-hover:border-indigo-500 transition-colors shadow-sm mb-4">
                            <?php if(!empty($mbr['photo_path'])): ?>
                                <img src="<?php echo htmlspecialchars($mbr['photo_path']); ?>" alt="<?php echo htmlspecialchars($mbr['name']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <?php else: ?>
                                <div class="w-full h-full bg-slate-200 flex items-center justify-center text-xl font-bold text-slate-500">
                                    <?php echo strtoupper(substr($mbr['name'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base leading-tight"><?php echo htmlspecialchars($mbr['name']); ?></h3>
                        <p class="text-xs text-indigo-600 font-medium mt-0.5"><?php echo htmlspecialchars($mbr['role']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- 6. DYNAMIC CONGREGATIONAL TESTIMONIES DISPLAY -->
    <section class="bg-slate-900 text-white py-20 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase text-indigo-400 tracking-wider">Testimonials</span>
                <h2 class="text-3xl font-extrabold tracking-tight text-white mt-1">Stories of Grace & Praise</h2>
            </div>
            
            <?php if(!empty($testimonies)): ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                    <?php foreach($testimonies as $tst): ?>
                        <div class="bg-slate-800 border border-slate-700/60 p-6 rounded-xl flex flex-col justify-between shadow-md">
                            <p class="text-slate-300 italic text-sm leading-relaxed mb-6">
                                "<?php echo htmlspecialchars($tst['message'] ?? $tst['content'] ?? ''); ?>"
                            </p>
                            <div>
                                <h4 class="font-bold text-white text-sm">
                                    <?php echo htmlspecialchars($tst['name'] ?? $tst['full_name'] ?? 'Anonymous'); ?>
                                </h4>
                                <span class="text-[11px] text-indigo-400 font-medium tracking-wide">Verified Witness</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- DEDICATED TESTIMONY CALL-TO-ACTION PORTAL -->
            <div class="w-full max-w-xl mx-auto <?php echo empty($testimonies) ? '' : 'mt-10'; ?>">
                <div class="bg-gradient-to-br from-slate-800 to-slate-850 border border-slate-700 rounded-2xl p-6 text-center shadow-xs">
                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-700 text-indigo-400 text-lg mb-3">
                        ✨
                    </div>
                    <h3 class="text-sm font-extrabold text-white mb-1 tracking-tight">
                        Has God done a miracle in your life?
                    </h3>
                    <p class="text-xs text-slate-400 max-w-md mx-auto leading-relaxed mb-4">
                        Your praise report has the power to anchor someone else's faith. Share your story safely with our ministry team.
                    </p>
                    <a href="submit-testimony.php" 
                       class="inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-xs transition-all focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-slate-900">
                        ✍️ Share Your Testimony
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!-- GLOBAL FRONTEND FOOTER -->
    <?php 
    if (file_exists('includes/footer.php')) {
        include_once 'includes/footer.php';
    } else {
    ?>
        <footer class="bg-white border-t border-slate-200 py-8 text-center text-xs text-slate-500 mt-auto">
            <p>&copy; <?php echo date('Y'); ?> Life Changers Ministry. All Rights Reserved. Built with faith & dedication.</p>
        </footer>
    <?php } ?>

</body>
</html>