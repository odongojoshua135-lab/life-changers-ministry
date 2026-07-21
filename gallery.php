<?php
// gallery.php
require_once 'config/db.php';

// 1. Fetch all distinct categories for the filter buttons
$catStmt = $pdo->query("SELECT DISTINCT category FROM gallery WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

// 2. Determine if a specific category filter is requested
$selectedCategory = isset($_GET['category']) ? trim($_GET['category']) : '';

// 3. Fetch images based on filter selection
if (!empty($selectedCategory)) {
    $galleryStmt = $pdo->prepare("SELECT * FROM gallery WHERE category = ? ORDER BY id DESC");
    $galleryStmt->execute([$selectedCategory]);
} else {
    $galleryStmt = $pdo->query("SELECT * FROM gallery ORDER BY id DESC");
}
$images = $galleryStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Gallery - Life Changers Ministry</title>
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
                <div class="hidden md:flex space-x-8 text-sm font-semibold text-slate-600">
                    <a href="index.php" class="hover:text-indigo-600 transition-colors">Home</a>
                    <a href="index.php#about" class="hover:text-indigo-600 transition-colors">About Us</a>
                    <a href="gallery.php" class="text-indigo-600 hover:text-indigo-700 transition-colors">Gallery</a>
                    <a href="events.php" class="hover:text-indigo-600 transition-colors">Events</a>
                    <a href="blog.php" class="hover:text-indigo-600 transition-colors">Blogs</a>
                </div>
                <div>
                    <a href="donate.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-full text-sm font-bold tracking-wide shadow-sm transition-all hover:shadow-md">Partner / Donate</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- HEADER BLOCK -->
    <header class="bg-slate-900 text-white py-16 relative overflow-hidden text-center">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#4f46e5_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="relative max-w-4xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black tracking-tight mb-3">Ministry Media Gallery</h1>
            <p class="text-slate-400 max-w-xl mx-auto text-sm sm:text-base font-normal">
                Glimpses of fellowship, outpourings of grace, and community moments captured in time.
            </p>
        </div>
    </header>

    <!-- MAIN GALLERY FRAMEWORK -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        <!-- CATEGORY FILTER PILLS -->
        <?php if(!empty($categories)): ?>
            <div class="flex flex-wrap justify-center items-center gap-2 mb-12">
                <a href="gallery.php" class="px-4 py-1.5 rounded-full text-xs font-bold transition-all border <?php echo empty($selectedCategory) ? 'bg-indigo-600 border-indigo-600 text-white shadow-xs' : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300'; ?>">
                    All Moments
                </a>
                <?php foreach($categories as $cat): ?>
                    <a href="gallery.php?category=<?php echo urlencode($cat); ?>" class="px-4 py-1.5 rounded-full text-xs font-bold transition-all border <?php echo ($selectedCategory === $cat) ? 'bg-indigo-600 border-indigo-600 text-white shadow-xs' : 'bg-white border-slate-200 text-slate-600 hover:border-slate-300'; ?>">
                        <?php echo htmlspecialchars($cat); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- IMAGES CONTAINER GRID -->
        <?php if(empty($images)): ?>
            <div class="text-center py-20 bg-white border border-slate-200 rounded-2xl shadow-xs max-w-xl mx-auto px-6">
                <span class="text-3xl">🖼️</span>
                <h3 class="mt-4 text-base font-bold text-slate-900">No media entries found</h3>
                <p class="mt-1 text-sm text-slate-500">There are no uploaded images matching this structural configuration right now.</p>
                <a href="gallery.php" class="inline-block mt-5 text-xs font-bold text-indigo-600 hover:underline">&larr; Return to all moments</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach($images as $img): ?>
                    <div class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden group hover:shadow-md transition-all flex flex-col">
                        
                        <!-- Image Container with Aspect Constraints -->
                        <div class="aspect-video w-full bg-slate-100 overflow-hidden relative">
                            <?php if(!empty($img['image_path'])): ?>
                                <img src="<?php echo htmlspecialchars($img['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($img['title'] ?? 'Gallery Image'); ?>" 
                                     class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-500 loading='lazy'">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-slate-400 text-xs font-mono bg-slate-200">
                                    [Missing Image Path]
                                </div>
                            <?php endif; ?>
                            
                            <!-- Category Badge Floating over Image -->
                            <?php if(!empty($img['category'])): ?>
                                <span class="absolute bottom-3 left-3 px-2.5 py-1 bg-slate-950/70 backdrop-blur-xs text-white text-[10px] font-bold uppercase tracking-wider rounded-md">
                                    <?php echo htmlspecialchars($img['category']); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Info Metadata Text Block -->
                        <div class="p-5 flex-grow flex flex-col justify-between">
                            <div>
                                <h3 class="font-bold text-slate-900 text-base leading-snug mb-1 group-hover:text-indigo-600 transition-colors">
                                    <?php echo htmlspecialchars($img['title']); ?>
                                </h3>
                                <?php if(!empty($img['description'])): ?>
                                    <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                        <?php echo htmlspecialchars($img['description']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Upload Date Timestamp info line -->
                            <?php if(isset($img['created_at'])): ?>
                                <div class="mt-4 pt-3 border-t border-slate-100 text-[10px] text-slate-400 font-medium">
                                    Added on <?php echo date('M d, Y', strtotime($img['created_at'])); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>

    <!-- GLOBAL FRONTEND FOOTER -->
    <footer class="bg-white border-t border-slate-200 py-8 text-center text-xs text-slate-500 mt-auto">
        <p>&copy; <?php echo date('Y'); ?> Life Changers Ministry. All Rights Reserved.</p>
    </footer>

</body>
</html>