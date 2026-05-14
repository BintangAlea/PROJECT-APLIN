/* ============================================
   MERISH - JavaScript
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle (if needed in future)
    initMobileMenu();
    
    // Smooth scrolling for navigation links
    initSmoothScroll();
    
    // Add animations on scroll
    initScrollAnimations();
});

/**
 * Initialize mobile menu (for future mobile nav)
 */
function initMobileMenu() {
    const header = document.querySelector('header');
    const nav = document.querySelector('nav');
    
    // Check if hamburger menu is needed on mobile
    window.addEventListener('resize', function() {
        updateMenuVisibility();
    });
    
    updateMenuVisibility();
}

/**
 * Update menu visibility based on screen size
 */
function updateMenuVisibility() {
    const nav = document.querySelector('nav');
    if (window.innerWidth <= 768) {
        // Mobile menu logic here
    }
}

/**
 * Smooth scrolling for anchor links
 */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && document.querySelector(href)) {
                e.preventDefault();
                document.querySelector(href).scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

/**
 * Add animations on scroll
 */
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe animation targets
    document.querySelectorAll('.fade-in').forEach(el => {
        observer.observe(el);
    });
}

/**
 * Helper: Format currency
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}

/**
 * Helper: Show toast notification
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/**
 * Helper: Check if authenticated
 */
function isAuthenticated() {
    // This would typically come from PHP session
    return !!document.body.dataset.authenticated;
}

/**
 * Helper: Redirect to login
 */
function redirectToLogin() {
    window.location.href = '/SIB/PROJECT-APLIN/router.php?route=auth/login';
}
