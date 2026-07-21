<?php
// blog.php
require_once 'config/db.php';

$article = null;
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

// --- ROUTINE A: IF SINGLE POST SLUG IS REQUESTED ---
if (!empty($slug)) {
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE slug = ? LIMIT 1");
    $stmt->execute([$slug]);
    $article = $stmt->fetch();
}

// --- ROUTINE B: IF CATALOG VIEW IS ACTIVE ---
if (!$article) {
    // Fetch all blogs, newest first
    $catalogStmt = $pdo->query("SELECT * FROM blogs ORDER BY id DESC");
    $posts = $catalogStmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $article ? htmlspecialchars($article['title']) . " - Life Changers" : "Ministry Blog & Insights - Life Changers"; ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased flex flex-col min-h-screen">

    <!-- GLOBAL NAVIGATION BAR -->
    <nav class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0">
                    <a href="index.php" class="text-xl font-extrabold tracking-tight text-indigo-600">Life Changers Ministry</a>
                </div>
                <!-- UPDATED GLOBAL NAV MENU LINKS -->
                <div class="hidden md:flex space-x-8 text-sm font-semibold text-slate-600">
                    <a href="index.php" class="hover:text-indigo-600 transition-colors">Home</a>
                    <a href="about.php" class="hover:text-indigo-600 transition-colors">About Us</a>
                    <a href="activities.php" class="hover:text-indigo-600 transition-colors">Activities</a>
                    <a href="blog.php" class="text-indigo-600 hover:text-indigo-700 transition-colors">Blogs</a>
                    <a href="contact.php" class="hover:text-indigo-600 transition-colors">Contact</a>
                </div>
                <div>
                    <a href="donate.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-full text-sm font-bold tracking-wide shadow-sm transition-all hover:shadow-md">Partner / Donate</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- ========================================== -->
    <!--  RENDER CHOICE 1: FULL SINGLE ARTICLE VIEW -->
    <!-- ========================================== -->
    <?php if ($article): ?>
        <main class="flex-grow py-12 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto w-full">
            <!-- Back navigation breadcrumb link -->
            <a href="blog.php" class="inline-flex items-center text-xs font-bold text-indigo-600 hover:text-indigo-700 transition-colors mb-6">
                &larr; Back to Articles Catalog
            </a>

            <article class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden p-6 sm:p-10">
                <!-- Metadata header block -->
                <header class="mb-8 border-b border-slate-100 pb-6">
                    <h1 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight leading-tight mb-4">
                        <?php echo htmlspecialchars($article['title']); ?>
                    </h1>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-slate-400 font-medium">
                        <span>✍️ By <strong><?php echo htmlspecialchars($article['author'] ?? 'Ministry Team'); ?></strong></span>
                        <span>•</span>
                        <span>🗓️ Published: <strong><?php echo date('F d, Y', strtotime($article['created_at'])); ?></strong></span>
                    </div>
                </header>

                <!-- Full Resolution Hero Cover Image aspect view frame -->
                <?php if (!empty($article['cover_image'])): ?>
                    <div class="aspect-video w-full rounded-xl overflow-hidden mb-8 border border-slate-200 bg-slate-50">
                        <img src="<?php echo htmlspecialchars($article['cover_image']); ?>" class="w-full h-full object-cover">
                    </div>
                <?php endif; ?>

                <!-- Main Content Prose Output Body -->
                <div class="text-slate-700 leading-relaxed text-base space-y-4 font-normal whitespace-pre-line">
                    <?php echo htmlspecialchars($article['content']); ?>
                </div>
            </article>
        </main>

    <!-- ========================================== -->
    <!--  RENDER CHOICE 2: GRID COMPREHENSIVE CATALOG -->
    <!-- ========================================== -->
    <?php else: ?>
        <!-- HEADER BLOCK -->
        <header class="bg-slate-900 text-white py-16 relative overflow-hidden text-center">
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#4f46e5_1px,transparent_1px)] [background-size:16px_16px]"></div>
            <div class="relative max-w-4xl mx-auto px-4">
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight mb-3">Ministry Blog & Insights</h1>
                <p class="text-slate-400 max-w-xl mx-auto text-sm sm:text-base font-normal">
                    Spiritual food, pastoral letters, field journals, and inspirational publications.
                </p>
            </div>
        </header>

        <!-- CATALOG DECK LAYOUT WINDOW -->
        <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <?php if (empty($posts)): ?>
                <div class="text-center py-20 bg-white border border-slate-200 rounded-2xl shadow-xs max-w-md mx-auto px-4">
                    <span class="text-2xl">📝</span>
                    <h3 class="mt-3 font-bold text-slate-900 text-sm">No blog entries published yet</h3>
                    <p class="text-xs text-slate-500 mt-1">Check back soon for upcoming dynamic spiritual reflections and publications.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <?php foreach ($posts as $post): ?>
                        <div class="bg-white rounded-xl border border-slate-200 shadow-2xs overflow-hidden flex flex-col justify-between hover:border-slate-300 hover:shadow-xs transition-all group">
                            
                            <!-- Thumbnail Cover Frame box link -->
                            <a href="blog.php?slug=<?php echo urlencode($post['slug']); ?>" class="aspect-video w-full bg-slate-100 block overflow-hidden relative border-b border-slate-150">
                                <?php if (!empty($post['cover_image'])): ?>
                                    <img src="<?php echo htmlspecialchars($post['cover_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($post['title']); ?>" 
                                         class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-300" loading="lazy">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center bg-slate-200 font-bold text-slate-400 text-xl">
                                        📖
                                    </div>
                                <?php endif; ?>
                            </a>

                            <!-- Summary info Text Content blocks details fields -->
                            <div class="p-5 flex-grow flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center gap-2 text-[10px] font-medium text-slate-400 mb-2">
                                        <span>🗓️ <?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                                    </div>
                                    <h3 class="font-bold text-base text-slate-900 leading-snug line-clamp-2 mb-2 group-hover:text-indigo-600 transition-colors">
                                        <a href="blog.php?slug=<?php echo urlencode($post['slug']); ?>">
                                            <?php echo htmlspecialchars($post['title']); ?>
                                        </a>
                                    </h3>
                                    <p class="text-xs text-slate-500 line-clamp-3 leading-relaxed mb-4">
                                        <?php echo htmlspecialchars(strip_tags($post['content'])); ?>
                                    </p>
                                </div>

                                <!-- Post Footer Link block actions metadata rows -->
                                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                                    <span>By <strong><?php echo htmlspecialchars($post['author'] ?? 'Ministry Team'); ?></strong></span>
                                    <!-- CRITICAL CONNECTION LINK TO DEDICATED TEMPLATE INTERFACE WITH SHIFT ANIMATION -->
                                    <a href="blog.php?slug=<?php echo urlencode($post['slug']); ?>" class="inline-flex items-center text-xs font-bold text-indigo-600 hover:text-indigo-700 transition-colors group/btn">
                                        Read Full Article
                                        <span class="transform translate-x-0 group-hover/btn:translate-x-1 transition-transform ml-1">&rarr;</span>
                                    </a>
                                </div>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    <?php endif; ?>

    <!-- GLOBAL FRONTEND FOOTER -->
    <footer class="bg-white border-t border-slate-200 py-8 text-center text-xs text-slate-500 mt-auto">
        <p>&copy; <?php echo date('Y'); ?> Life Changers Ministry. All Rights Reserved.</p>
    </footer>

</body>
</html>