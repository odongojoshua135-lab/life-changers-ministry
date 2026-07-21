<?php
// blog-single.php
require_once 'config/db.php';
$current_page = 'blog';

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$article = null;

if (!empty($slug)) {
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE slug = ? LIMIT 1");
    $stmt->execute([$slug]);
    $article = $stmt->fetch();
}

if (!$article) {
    header("Location: blog.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> - Life Changers Ministry</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased flex flex-col min-h-screen">

    <?php include_once 'includes/navbar.php'; ?>

    <!-- CONTENT DISPLAY FRAME -->
    <main class="flex-grow py-12 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto w-full">
        <a href="blog.php" class="inline-flex items-center text-xs font-bold text-indigo-600 hover:text-indigo-700 transition-colors mb-6">
            &larr; Back to Articles Catalog
        </a>

        <article class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden p-6 sm:p-10">
            <header class="mb-6 border-b border-slate-100 pb-6">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight mb-4">
                    <?php echo htmlspecialchars($article['title']); ?>
                </h1>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-slate-400 font-medium">
                    <span>✍️ By <strong><?php echo htmlspecialchars($article['author'] ?? 'Ministry Team'); ?></strong></span>
                    <span>•</span>
                    <span>🗓️ Published: <strong><?php echo date('F d, Y', strtotime($article['created_at'])); ?></strong></span>
                </div>
            </header>

            <?php if (!empty($article['cover_image'])): ?>
                <div class="aspect-video w-full rounded-xl overflow-hidden mb-8 border border-slate-200 bg-slate-50">
                    <img src="<?php echo htmlspecialchars($article['cover_image']); ?>" class="w-full h-full object-cover">
                </div>
            <?php endif; ?>

            <div class="text-slate-700 leading-relaxed text-sm sm:text-base space-y-4 whitespace-pre-line font-normal">
                <?php echo htmlspecialchars($article['content']); ?>
            </div>
        </article>
    </main>

    <?php include_once 'includes/footer.php'; ?>