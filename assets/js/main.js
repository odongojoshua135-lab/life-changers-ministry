// js/main.js
document.addEventListener('DOMContentLoaded', function() {
    
    // Auto-dismiss execution status banner alert modules after a set window
    const dynamicBanners = document.querySelectorAll('.rounded-lg[class*="bg-"]');
    if (dynamicBanners.length > 0) {
        setTimeout(function() {
            dynamicBanners.forEach(function(banner) {
                // Apply a graceful fade-out transition
                banner.style.transition = 'opacity 0.5s cubic-bezier(0.4, 0, 0.2, 1), transform 0.5s';
                banner.style.opacity = '0';
                banner.style.transform = 'translateY(-8px)';
                
                setTimeout(function() {
                    banner.remove();
                }, 500);
            });
        }, 5000); // Triggers automatically after 5 seconds
    }

    // Form confirmation guard patterns for critical action blocks
    const dangerousActions = document.querySelectorAll('.btn-danger, [data-confirm]');
    dangerousActions.forEach(function(button) {
        button.addEventListener('click', function(event) {
            const warningPrompt = button.getAttribute('data-confirm') || 'Are you absolutely sure you want to proceed with this destructive operation?';
            if (!confirm(warningPrompt)) {
                event.preventDefault();
            }
        });
    });
});