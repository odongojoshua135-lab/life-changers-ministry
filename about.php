<?php
// about.php
require_once 'config/db.php';
$current_page = 'about'; 
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Our Vision - Life Changers Ministry</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased flex flex-col min-h-screen">

    <?php include_once 'includes/navbar.php'; ?>

    <!-- HEADER BLOCK -->
    <header class="bg-slate-900 text-white py-16 text-center relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#4f46e5_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="relative max-w-4xl mx-auto px-4">
            <h1 class="text-3xl sm:text-4xl font-black tracking-tight mb-3">Our Vision & Mission</h1>
            <p class="text-slate-400 max-w-xl mx-auto text-sm sm:text-base font-normal">
                Discover the heartbeat behind Life Changers Ministry and our commitment to transformation.
            </p>
        </div>
    </header>

    <!-- CONTENT BODY -->
    <main class="flex-grow max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
        <section class="bg-white border border-slate-200 rounded-xl p-8 shadow-2xs">
            <h2 class="text-xl font-bold text-slate-900 mb-4 border-b border-slate-100 pb-2">Who We Are</h2>
            <p class="text-sm text-slate-600 leading-relaxed mb-4">
                Life Changers Ministry is dedicated to preaching the gospel, raising disciples, and empowering local communities. We believe in practical Christianity that impacts lives spiritually, socially, and economically.
            </p>
        </section>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white border border-slate-200 rounded-xl p-6">
                <span class="text-2xl">🎯</span>
                <h3 class="font-bold text-slate-900 mt-2 mb-1 text-sm">Our Mission</h3>
                <p class="text-xs text-slate-500 leading-relaxed">To transform communities through the love of Christ, providing spiritual mentorship and critical community support initiatives.</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl p-6">
                <span class="text-2xl">👁️</span>
                <h3 class="font-bold text-slate-900 mt-2 mb-1 text-sm">Our Vision</h3>
                <p class="text-xs text-slate-500 leading-relaxed">A thriving, Christ-centered generation driving positive socioeconomic change across the entire region.</p>
            </div>
        </div>
    </main>

    <?php include_once 'includes/footer.php'; ?>