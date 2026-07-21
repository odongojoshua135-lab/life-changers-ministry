<?php
// events.php
require_once 'config/db.php';
$current_page = 'events';

// Fetch all upcoming/active events ordered by date
$stmt = $pdo->query("SELECT * FROM events ORDER BY event_date ASC");
$events = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Events - Life Changers Ministry</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased flex flex-col min-h-screen">

    <?php include_once 'includes/navbar.php'; ?>

    <main class="flex-grow max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <header class="mb-10 border-b border-slate-200 pb-4">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Upcoming Schedule</h1>
            <p class="text-sm text-slate-500 mt-1">Plan ahead and join us live for these scheduled programs.</p>
        </header>

        <?php if (empty($events)): ?>
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-xs">
                <p class="text-slate-500 text-sm">No upcoming events are currently scheduled. Please check back soon!</p>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($events as $event): ?>
                    <?php 
                        // 1. Resolve raw flyer path, giving priority to 'banner_path' as saved by admin/events.php
                        $rawFlyer = $event['banner_path'] ?? $event['flyer'] ?? $event['image'] ?? $event['flyer_path'] ?? $event['image_path'] ?? null;
                        
                        // 2. Normalize image path cleanly without broken directory prefixes
                        $flyer = null;
                        if (!empty($rawFlyer)) {
                            $rawFlyer = trim($rawFlyer);
                            if (str_starts_with($rawFlyer, 'http://') || str_starts_with($rawFlyer, 'https://') || str_starts_with($rawFlyer, '/')) {
                                $flyer = $rawFlyer;
                            } elseif (str_starts_with($rawFlyer, 'uploads/')) {
                                $flyer = $rawFlyer;
                            } else {
                                $flyer = 'uploads/events/' . $rawFlyer;
                            }
                        }

                        // 3. Safe time formatting
                        $rawTime = $event['event_time'] ?? $event['time'] ?? $event['start_time'] ?? null;
                        $formattedTime = !empty($rawTime) ? date('g:i A', strtotime($rawTime)) : '12:00 AM';
                    ?>
                    
                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs flex flex-col md:flex-row items-stretch hover:shadow-md transition-shadow">
                        
                        <!-- 1. DATE BADGE PANEL -->
                        <div class="bg-indigo-50/70 border-b md:border-b-0 md:border-r border-indigo-100/80 p-6 flex flex-col justify-center items-center shrink-0 w-full md:w-36 text-center">
                            <span class="text-xs font-bold uppercase text-indigo-600 tracking-widest">
                                <?php echo date('M', strtotime($event['event_date'])); ?>
                            </span>
                            <span class="text-3xl font-black text-slate-900 leading-tight">
                                <?php echo date('d', strtotime($event['event_date'])); ?>
                            </span>
                            <span class="text-xs text-slate-400 font-medium mt-1">
                                <?php echo date('Y', strtotime($event['event_date'])); ?>
                            </span>
                        </div>

                        <!-- 2. EVENT FLYER IMAGE PANEL -->
                        <?php if (!empty($flyer)): ?>
                            <div class="w-full md:w-64 h-56 md:h-auto shrink-0 bg-slate-100 border-b md:border-b-0 md:border-r border-slate-200 overflow-hidden flex items-center justify-center">
                                <img src="<?php echo htmlspecialchars($flyer); ?>" 
                                     alt="<?php echo htmlspecialchars($event['title']); ?>" 
                                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                     onerror="this.onerror=null; this.parentElement.style.display='none';"> 
                            </div>
                        <?php endif; ?>

                        <!-- 3. DETAILS & DESCRIPTION CONTENT -->
                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 tracking-tight mb-2">
                                    <?php echo htmlspecialchars($event['title']); ?>
                                </h3>
                                <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line mb-4">
                                    <?php echo htmlspecialchars($event['description']); ?>
                                </p>
                            </div>

                            <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center gap-6 text-xs text-slate-500">
                                <div class="flex items-center gap-1.5 font-medium">
                                    <span>⏰</span> Time: <strong class="text-slate-700"><?php echo $formattedTime; ?></strong>
                                </div>
                                <div class="flex items-center gap-1.5 font-medium">
                                    <span>📍</span> Location: <strong class="text-slate-700"><?php echo htmlspecialchars($event['location'] ?? 'Main Sanctuary'); ?></strong>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include_once 'includes/footer.php'; ?>
</body>
</html>