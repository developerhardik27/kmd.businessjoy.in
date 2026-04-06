/**
 * Sidebar Toggle Functionality
 * Handles expand/collapse of sidebar for all screen sizes
 */

document.addEventListener('DOMContentLoaded', function() {
    initSidebarToggle();
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            initSidebarToggle();
        }, 250);
    });
});

function initSidebarToggle() {
    const isDesktop = window.innerWidth >= 1300;
    const toggleBtn = document.querySelector('.sidebar-toggle-btn');
    
    if (isDesktop) {
        // Desktop: Show our custom toggle button
        if (toggleBtn) {
            toggleBtn.style.display = 'flex';
        }
        restoreSidebarState();
    } else {
        // Mobile/Tablet: Hide custom toggle, use existing menu button
        if (toggleBtn) {
            toggleBtn.style.display = 'none';
        }
        // Use existing mobile menu functionality
        document.body.classList.remove('sidebar-collapsed');
    }
}

function toggleSidebar() {
    // Only work on desktop screens
    if (window.innerWidth < 1300) return;
    
    const body = document.body;
    const isCollapsed = body.classList.contains('sidebar-collapsed');
    
    if (isCollapsed) {
        // Expand sidebar
        body.classList.remove('sidebar-collapsed');
        updateToggleButton(false);
        localStorage.setItem('sidebarState', 'expanded');
    } else {
        // Collapse sidebar
        body.classList.add('sidebar-collapsed');
        updateToggleButton(true);
        localStorage.setItem('sidebarState', 'collapsed');
    }
    
    // Force a reflow to ensure transitions work
    void body.offsetWidth;
}

function updateToggleButton(isCollapsed) {
    const toggleIcon = document.getElementById('sidebar-toggle-icon');
    const toggleBtn = document.querySelector('.sidebar-toggle-btn');
    
    if (!toggleIcon || !toggleBtn) return;
    
    if (isCollapsed) {
        toggleIcon.textContent = '☰';
        toggleBtn.setAttribute('title', 'Expand Sidebar');
    } else {
        toggleIcon.textContent = '✕';
        toggleBtn.setAttribute('title', 'Collapse Sidebar');
    }
}

// Restore saved sidebar state (only for desktop)
function restoreSidebarState() {
    if (window.innerWidth < 1300) return;
    
    const savedState = localStorage.getItem('sidebarState');
    
    // Default to OPEN (expanded) state
    if (savedState === 'collapsed') {
        document.body.classList.add('sidebar-collapsed');
        updateToggleButton(true);
    } else {
        // Default: sidebar is OPEN (expanded)
        document.body.classList.remove('sidebar-collapsed');
        updateToggleButton(false);
    }
}
