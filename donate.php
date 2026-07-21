<?php
// donate.php
require_once 'config/db.php';

// Fetch recent public donations or partner logs if available to show impact
try {
    // Attempting to pull the latest 4 approved/completed rows to encourage others
    $donationsStmt = $pdo->query("SELECT name, amount, created_at FROM donations WHERE status = 'completed' ORDER BY id DESC LIMIT 4");
    $recentDonations = $donationsStmt->fetchAll();
} catch (PDOException $e) {
    // Fallback if the table schema varies slightly
    $recentDonations = [];
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner & Donate - Life Changers Ministry</title>
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
                    <a href="gallery.php" class="hover:text-indigo-600 transition-colors">Gallery</a>
                    <a href="events.php" class="hover:text-indigo-600 transition-colors">Events</a>
                    <a href="blog.php" class="hover:text-indigo-600 transition-colors">Blogs</a>
                </div>
                <div>
                    <a href="donate.php" class="bg-indigo-600 text-white px-5 py-2 rounded-full text-sm font-bold tracking-wide shadow-sm">Partner / Donate</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- HEADER BLOCK -->
    <header class="bg-slate-900 text-white py-16 relative overflow-hidden text-center">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#4f46e5_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="relative max-w-4xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black tracking-tight mb-3">Support the Ministry</h1>
            <p class="text-slate-400 max-w-xl mx-auto text-sm sm:text-base font-normal">
                Your generosity empowers our community outreach programs, youth fellowships, and daily operations.
            </p>
        </div>
    </header>

    <!-- MAIN BODY SECTION -->
    <main class="flex-grow max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <!-- LEFT columns: PAYMENT PARAMETERS CARD matrix -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- OPTION A: MOBILE MONEY -->
                <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs">
                    <div class="flex items-center space-x-3 mb-4">
                        <span class="text-2xl">📱</span>
                        <h2 class="text-lg font-bold text-slate-900">Mobile Money Transfers</h2>
                    </div>
                    <p class="text-xs text-slate-500 mb-6">Send your support directly via local mobile transfer networks. Please ensure the registered name matches before approving payment.</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- MTN Card -->
                        <div class="p-4 rounded-lg bg-amber-50 border border-amber-200 flex flex-col justify-between">
                            <div>
                                <span class="text-[10px] uppercase tracking-wider font-bold text-amber-700 bg-amber-200/50 px-2 py-0.5 rounded">MTN MoMo</span>
                                <div class="text-xl font-mono font-bold text-slate-900 mt-3 tracking-wide">077XXXXXXX</div>
                            </div>
                            <div class="text-xs text-slate-600 mt-4 pt-2 border-t border-amber-200/60">
                                Registered: <strong class="text-slate-900">LIFE CHANGERS MIN...</strong>
                            </div>
                        </div>

                        <!-- Airtel Card -->
                        <div class="p-4 rounded-lg bg-red-50 border border-red-200 flex flex-col justify-between">
                            <div>
                                <span class="text-[10px] uppercase tracking-wider font-bold text-red-700 bg-red-200/50 px-2 py-0.5 rounded">Airtel Money</span>
                                <div class="text-xl font-mono font-bold text-slate-900 mt-3 tracking-wide">070XXXXXXX</div>
                            </div>
                            <div class="text-xs text-slate-600 mt-4 pt-2 border-t border-red-200/60">
                                Registered: <strong class="text-slate-900">LIFE CHANGERS MIN...</strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 bg-slate-50 border border-slate-200 rounded-lg p-3 text-xs text-slate-600 flex items-start">
                        <span class="mr-2">💡</span>
                        <p>Use your <strong>Name</strong> or <strong>"Partner"</strong> as the reference text code so the admin matching ledger can identify your payment quickly.</p>
                    </div>
                </div>

                <!-- OPTION B: BANK DETAILS -->
                <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs">
                    <div class="flex items-center space-x-3 mb-4">
                        <span class="text-2xl">🏢</span>
                        <h2 class="text-lg font-bold text-slate-900">Direct Bank Wire / Transfer</h2>
                    </div>
                    <p class="text-xs text-slate-500 mb-4">Ideal for structured institutional giving, monthly recurring tithes, or international wire routing systems.</p>
                    
                    <div class="border border-slate-100 rounded-lg overflow-hidden text-sm">
                        <div class="grid grid-cols-3 border-b border-slate-100 p-3 bg-slate-50/70 font-medium text-slate-500 text-xs">
                            <div class="col-span-1">Field Description</div>
                            <div class="col-span-2">Account Routing Configuration</div>
                        </div>
                        <div class="grid grid-cols-3 border-b border-slate-100 p-3">
                            <div class="text-slate-500 font-medium text-xs">Bank Name</div>
                            <div class="col-span-2 text-slate-900 font-semibold">Stanbic Bank (Uganda) Limited</div>
                        </div>
                        <div class="grid grid-cols-3 border-b border-slate-100 p-3">
                            <div class="text-slate-500 font-medium text-xs">Account Name</div>
                            <div class="col-span-2 text-slate-900">LIFE CHANGERS MINISTRY SYSTEM</div>
                        </div>
                        <div class="grid grid-cols-3 border-b border-slate-100 p-3">
                            <div class="text-slate-500 font-medium text-xs">Account Number</div>
                            <div class="col-span-2 font-mono text-slate-900 font-bold tracking-wide">90300XXXXXXXX</div>
                        </div>
                        <div class="grid grid-cols-3 p-3">
                            <div class="text-slate-500 font-medium text-xs">Branch / SWIFT</div>
                            <div class="col-span-2 font-mono text-slate-700">Corporate Branch / SBICUGKX</div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: RECENT GIVING LIVE TRACK FEED -->
            <div class="space-y-6">
                <div class="bg-indigo-900 text-white rounded-xl p-6 shadow-xs relative overflow-hidden">
                    <div class="absolute right-0 bottom-0 opacity-5 text-8xl translate-x-4 translate-y-4 select-none pointer-events-none font-bold">🤝</div>
                    <h3 class="font-bold text-base mb-2">Become a Regular Covenant Partner</h3>
                    <p class="text-xs text-indigo-200 leading-relaxed mb-4">
                        You can set up standalone configurations with your banking platform for consistent weekly or monthly operational support targets.
                    </p>
                    <a href="index.php#about" class="text-xs font-bold bg-white/10 hover:bg-white/20 border border-white/20 rounded px-3 py-1.5 transition-colors inline-block">
                        Read Our Vision &rarr;
                    </a>
                </div>

                <!-- Recent donations ticker loop panel -->
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <h4 class="text-xs uppercase font-bold text-slate-400 tracking-wider mb-4">Recent Giving Stream</h4>
                    
                    <?php if(empty($recentDonations)): ?>
                        <p class="text-slate-400 text-xs text-center py-6 italic">Gifts and tithes tracker updates automatically.</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach($recentDonations as $don): ?>
                                <div class="flex justify-between items-start text-xs border-b border-slate-50 pb-3 last:border-0 last:pb-0">
                                    <div>
                                        <div class="font-bold text-slate-800">
                                            <?php echo htmlspecialchars($don['name'] ?: 'Anonymous Partner'); ?>
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">
                                            <?php echo date('M d, Y', strtotime($don['created_at'])); ?>
                                        </div>
                                    </div>
                                    <div class="font-mono font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">
                                        +<?php echo number_format($don['amount']); ?> UGX
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>

    <!-- GLOBAL FRONTEND FOOTER -->
    <footer class="bg-white border-t border-slate-200 py-8 text-center text-xs text-slate-500 mt-auto">
        <p>&copy; <?php echo date('Y'); ?> Life Changers Ministry. All Rights Reserved.</p>
    </footer>

</body>
</html>