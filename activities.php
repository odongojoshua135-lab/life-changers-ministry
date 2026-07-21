<?php
// activities.php
require_once 'config/db.php';
$current_page = 'activities';
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Activities - Life Changers Ministry</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased flex flex-col min-h-screen">

    <?php include_once 'includes/navbar.php'; ?>

    <!-- HEADER BLOCK -->
    <header class="bg-slate-900 text-white py-16 text-center relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#4f46e5_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="relative max-w-4xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black tracking-tight mb-3">Ministry Activities</h1>
            <p class="text-slate-400 max-w-xl mx-auto text-sm sm:text-base font-normal">
                Discover our structured operational arms designed to serve every age group and community tier.
            </p>
        </div>
    </header>

    <!-- MAIN GRID CONTAINER -->
    <main class="flex-grow max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- CARD 1 -->
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-2xs flex flex-col justify-between">
                <div>
                    <span class="text-3xl">🛡️</span>
                    <h3 class="text-base font-bold text-slate-900 mt-4 mb-2">Youth Fellowship & Mentorship</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Equipping the next generation with deep spiritual foundations, leadership capacity building, and practical talent development matrices.
                    </p>
                </div>
                <div class="mt-6 text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded w-max">
                    Weekly • Saturdays
                </div>
            </div>

            <!-- CARD 2 -->
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-2xs flex flex-col justify-between">
                <div>
                    <span class="text-3xl">🌍</span>
                    <h3 class="text-base font-bold text-slate-900 mt-4 mb-2">Community Outreach Actions</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Extending support cards, medical camp modules, and basic food relief directly to disadvantaged families in surrounding neighborhoods.
                    </p>
                </div>
                <div class="mt-6 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded w-max">
                    Monthly Deployments
                </div>
            </div>

            <!-- CARD 3 -->
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-2xs flex flex-col justify-between">
                <div>
                    <span class="text-3xl">🔥</span>
                    <h3 class="text-base font-bold text-slate-900 mt-4 mb-2">Prayer & Word Altars</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Deep mid-week intercession gather points and intense Bible studies constructed to establish structural believers in their faith foundations.
                    </p>
                </div>
                <div class="mt-6 text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded w-max">
                    Mid-Week • Wednesdays
                </div>
            </div>

        </div>
    </main>

    <?php include_once 'includes/footer.php'; ?>