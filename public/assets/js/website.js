/**
 * Real Estate Website JavaScript
 *
 * This file contains all the client-side functionality for the real estate website
 * including form handling, animations, navigation, and user interactions.
 *
 * Dependencies:
 * - Bootstrap 5.3.2 (for modals, toasts, and components)
 * - Font Awesome 6.5.0 (for icons)
 *
 * @author Real Estate Team
 * @version 2.1
 * @since 2025-08-01
 * @updated 2025-08-04 - Fixed browser compatibility issues
 */

/**
 * Polyfill for Element.closest() method for older browsers
 * This ensures compatibility with Internet Explorer and older browsers
 */
if (!Element.prototype.closest) {
    Element.prototype.closest = function(selector) {
        var element = this;
        while (element && element.nodeType === 1) {
            if (element.matches && element.matches(selector)) {
                return element;
            }
            element = element.parentElement || element.parentNode;
        }
        return null;
    };
}

/**
 * Polyfill for Element.matches() method for older browsers
 */
if (!Element.prototype.matches) {
    Element.prototype.matches = Element.prototype.msMatchesSelector ||
                                Element.prototype.webkitMatchesSelector;
}

/**
 * Safe closest() function with fallback for maximum browser compatibility
 * @param {Element} element - The element to start searching from
 * @param {string} selector - The CSS selector to match
 * @returns {Element|null} - The closest matching element or null
 */
function safeClosest(element, selector) {
    // Return null if element is not valid
    if (!element || !element.nodeType) return null;

    // Use native closest if available
    if (element.closest) {
        return element.closest(selector);
    }

    // Fallback implementation for older browsers
    var current = element;
    while (current && current.nodeType === 1) {
        if (current.matches && current.matches(selector)) {
            return current;
        }
        current = current.parentElement || current.parentNode;
    }
    return null;
}

// Global cleanup registry for memory leak prevention
const cleanupRegistry = new Set();

/**
 * Register cleanup function to prevent memory leaks
 */
function registerCleanup(cleanupFn) {
    cleanupRegistry.add(cleanupFn);
}

/**
 * Execute all cleanup functions
 */
function executeCleanup() {
    cleanupRegistry.forEach(cleanupFn => {
        try {
            cleanupFn();
        } catch (error) {
            console.warn('Cleanup function failed:', error);
        }
    });
    cleanupRegistry.clear();
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all website components in order
    initializeNavbar();        // Navigation functionality
    initializeAnimations();    // Scroll animations and transitions
    initializeToasts();        // Toast notification system
    initializePropertyCards(); // Property card interactions
    initializeForms();         // Form validation and Ajax submissions
    initializeHeroCarousel();  // Modern hero carousel functionality
    initializeTestimonials();  // Testimonials section functionality
    initializeImageHandling(); // Enhanced image loading and error handling

    console.log('Real Estate Website loaded successfully');
});

// Cleanup on page unload to prevent memory leaks
window.addEventListener('beforeunload', executeCleanup);
window.addEventListener('pagehide', executeCleanup);

/**
 * Centralized error handling system
 * Provides user-friendly error messages and logging
 */
const ErrorHandler = {
    /**
     * Handle and display user-friendly errors
     */
    handle: function(error, context = 'Application', userMessage = null) {
        // Log technical error for developers
        console.error(`[${context}] Error:`, error);

        // Show user-friendly message
        const friendlyMessage = userMessage || this.getFriendlyMessage(error);
        if (typeof showNotification === 'function') {
            showNotification(friendlyMessage, 'error');
        } else {
            // Fallback for when notification system isn't available
            alert(friendlyMessage);
        }
    },

    /**
     * Get user-friendly error message
     */
    getFriendlyMessage: function(error) {
        if (error.name === 'NetworkError' || error.message.includes('fetch')) {
            return 'Network connection issue. Please check your internet connection and try again.';
        }

        if (error.name === 'TypeError' && error.message.includes('closest')) {
            return 'Browser compatibility issue detected. Please try refreshing the page.';
        }

        if (error.message.includes('404')) {
            return 'The requested resource was not found. Please try again later.';
        }

        if (error.message.includes('500')) {
            return 'Server error occurred. Our team has been notified. Please try again later.';
        }

        return 'An unexpected error occurred. Please try refreshing the page or contact support if the issue persists.';
    },

    /**
     * Wrap function with error handling
     */
    wrap: function(fn, context = 'Function') {
        return function(...args) {
            try {
                return fn.apply(this, args);
            } catch (error) {
                ErrorHandler.handle(error, context);
                return null;
            }
        };
    }
};

// Global error handler for unhandled errors
window.addEventListener('error', function(event) {
    ErrorHandler.handle(event.error, 'Global', 'An unexpected error occurred. The page will continue to work, but some features may be affected.');
});

// Global handler for unhandled promise rejections
window.addEventListener('unhandledrejection', function(event) {
    ErrorHandler.handle(event.reason, 'Promise', 'A background operation failed. This may affect some features.');
    event.preventDefault(); // Prevent console error
});

/**
 * Initialize navbar functionality
 *
 * This function sets up comprehensive navbar behavior including:
 * - Scroll-based visual effects for better user experience
 * - Enhanced mobile menu with accessibility features
 * - Keyboard navigation support
 * - Click-outside-to-close functionality
 * - Haptic feedback for mobile devices
 * - Memory leak prevention through proper event cleanup
 *
 * The function ensures cross-browser compatibility and follows
 * WCAG 2.1 AA accessibility guidelines.
 *
 * @since 2.1.0 - Enhanced with accessibility and mobile improvements
 */
function initializeNavbar() {
    const navbar = document.querySelector('.navbar');
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');

    if (!navbar) return; // Exit if navbar not found

    // Add scroll effect to navbar for better visual hierarchy
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Enhanced mobile menu toggler functionality
    if (navbarToggler && navbarCollapse) {
        // Add ARIA labels for better accessibility
        navbarToggler.setAttribute('aria-label', 'Toggle navigation menu');

        // Handle toggler click with enhanced feedback
        navbarToggler.addEventListener('click', function() {
            // Add haptic feedback for mobile devices
            if (navigator.vibrate) {
                navigator.vibrate(50);
            }

            // Update ARIA label based on state
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-label', isExpanded ? 'Close navigation menu' : 'Open navigation menu');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            if (navbarCollapse.classList.contains('show') &&
                !navbar.contains(e.target)) {
                const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                if (bsCollapse) {
                    bsCollapse.hide();
                }
            }
        });

        // Close mobile menu on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && navbarCollapse.classList.contains('show')) {
                const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                if (bsCollapse) {
                    bsCollapse.hide();
                }
                navbarToggler.focus(); // Return focus to toggler
            }
        });
    }

    // Close mobile menu when clicking on links (improves UX on mobile)
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                if (bsCollapse) {
                    bsCollapse.hide();
                }
            }
        });

        // Add keyboard navigation support
        link.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });

    // Add smooth transitions for mobile menu
    if (navbarCollapse) {
        navbarCollapse.addEventListener('show.bs.collapse', function() {
            this.style.transition = 'height 0.35s ease';
        });

        navbarCollapse.addEventListener('hide.bs.collapse', function() {
            this.style.transition = 'height 0.35s ease';
        });
    }
}

/**
 * Initialize modern hero carousel
 *
 * Creates a smooth, auto-playing carousel with dot navigation.
 * Includes touch/swipe support for mobile devices and keyboard navigation.
 */
function initializeHeroCarousel() {
    const carousel = document.getElementById('heroCarousel');
    if (!carousel) return;

    const slides = carousel.querySelectorAll('.carousel-slide');
    const dots = carousel.querySelectorAll('.carousel-dot');
    let currentSlide = 0;
    let autoPlayInterval;

    // Auto-play interval (5 seconds)
    const AUTOPLAY_DELAY = 5000;

    /**
     * Show specific slide
     */
    function showSlide(index) {
        // Remove active class from all slides and dots
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));

        // Add active class to current slide and dot
        slides[index].classList.add('active');
        dots[index].classList.add('active');

        currentSlide = index;
    }

    /**
     * Go to next slide
     */
    function nextSlide() {
        const next = (currentSlide + 1) % slides.length;
        showSlide(next);
    }

    /**
     * Start auto-play
     */
    function startAutoPlay() {
        autoPlayInterval = setInterval(nextSlide, AUTOPLAY_DELAY);
    }

    /**
     * Stop auto-play
     */
    function stopAutoPlay() {
        if (autoPlayInterval) {
            clearInterval(autoPlayInterval);
            autoPlayInterval = null;
        }
    }

    // Add click handlers to dots
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            showSlide(index);
            stopAutoPlay();
            // Restart auto-play after user interaction
            setTimeout(startAutoPlay, 3000);
        });
    });

    // Pause auto-play on hover
    carousel.addEventListener('mouseenter', stopAutoPlay);
    carousel.addEventListener('mouseleave', startAutoPlay);

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            const prev = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(prev);
            stopAutoPlay();
            setTimeout(startAutoPlay, 3000);
        } else if (e.key === 'ArrowRight') {
            nextSlide();
            stopAutoPlay();
            setTimeout(startAutoPlay, 3000);
        }
    });

    // Touch/swipe support for mobile
    let touchStartX = 0;
    let touchEndX = 0;

    carousel.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    });

    carousel.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });

    function handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;

        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                // Swipe left - next slide
                nextSlide();
            } else {
                // Swipe right - previous slide
                const prev = (currentSlide - 1 + slides.length) % slides.length;
                showSlide(prev);
            }
            stopAutoPlay();
            setTimeout(startAutoPlay, 3000);
        }
    }

    // Start auto-play
    startAutoPlay();

    // Pause auto-play when page is not visible (prevents unnecessary processing)
    const visibilityChangeHandler = () => {
        if (document.hidden) {
            stopAutoPlay();
        } else {
            startAutoPlay();
        }
    };
    document.addEventListener('visibilitychange', visibilityChangeHandler);

    // Register cleanup function to prevent memory leaks
    registerCleanup(() => {
        stopAutoPlay();
        document.removeEventListener('visibilitychange', visibilityChangeHandler);
    });
}

/**
 * Initialize scroll animations
 */
function initializeAnimations() {
    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe elements for animation
    const animateElements = document.querySelectorAll('.card, .property-card, .hero-content, .testimonial-card');
    animateElements.forEach(el => {
        observer.observe(el);
    });
}

/**
 * Initialize toast notifications
 */
function initializeToasts() {
    // Auto-hide toasts after 5 seconds
    const toasts = document.querySelectorAll('.toast.show');
    toasts.forEach(toast => {
        setTimeout(() => {
            const bsToast = bootstrap.Toast.getOrCreateInstance(toast);
            bsToast.hide();
        }, 5000);
    });
}

/**
 * Initialize property card interactions
 *
 * Optimized to use event delegation to reduce memory usage
 * and improve performance with large numbers of property cards.
 * Updated with browser-compatible closest() implementation.
 */
function initializePropertyCards() {
    // Use event delegation for better performance and memory management
    document.addEventListener('mouseenter', function(e) {
        const propertyCard = safeClosest(e.target, '.property-card, .card');
        if (propertyCard) {
            propertyCard.style.transform = 'translateY(-8px)';
            propertyCard.style.transition = 'transform 0.3s ease';
        }
    }, true);

    document.addEventListener('mouseleave', function(e) {
        const propertyCard = safeClosest(e.target, '.property-card, .card');
        if (propertyCard) {
            propertyCard.style.transform = 'translateY(0)';
        }
    }, true);

    // Handle property view button clicks with event delegation
    document.addEventListener('click', function(e) {
        const viewButton = safeClosest(e.target, '[data-property-view]');
        if (viewButton) {
            e.preventDefault();
            const propertyId = viewButton.getAttribute('data-property-id');
            if (propertyId && typeof showPropertyModal === 'function') {
                showPropertyModal(propertyId);
            }
        }
    });
}

/**
 * Initialize form enhancements
 */
function initializeForms() {
    // Add floating label effect
    const formControls = document.querySelectorAll('.form-control, .form-select');
    
    formControls.forEach(control => {
        // Add focus/blur effects
        control.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        control.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
        
        // Check if already has value
        if (control.value) {
            control.parentElement.classList.add('focused');
        }
    });
    
    // Handle contact form submission (modal)
    const contactModalForm = document.getElementById('contactModalForm');
    if (contactModalForm) {
        contactModalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleContactFormSubmission(this, true);
        });
    }

    // Handle "Get In Touch" form submission (homepage)
    const getInTouchForm = document.getElementById('getInTouchForm');
    if (getInTouchForm) {
        getInTouchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleContactFormSubmission(this, false);
        });
    }

    // Handle contact page form submission
    const contactPageForm = document.getElementById('contactPageForm');
    if (contactPageForm) {
        contactPageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleContactFormSubmission(this, false);
        });
    }
    
    // Handle newsletter subscription
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleNewsletterSubmission(this);
        });
    }
}

/**
 * Show property modal
 */
function showPropertyModal(propertyId) {
    const modalHtml = `
        <div class="modal fade" id="propertyModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Property Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 250px;">
                                    <span class="text-muted">Property Image</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-semibold mb-3">Property Information</h6>
                                <div class="mb-2">
                                    <strong>Type:</strong> <span class="text-muted">House</span>
                                </div>
                                <div class="mb-2">
                                    <strong>Location:</strong> <span class="text-muted">Dublin, Ireland</span>
                                </div>
                                <div class="mb-2">
                                    <strong>Area:</strong> <span class="text-muted">2,500 sq ft</span>
                                </div>
                                <div class="mb-2">
                                    <strong>Bedrooms:</strong> <span class="text-muted">4</span>
                                </div>
                                <div class="mb-2">
                                    <strong>Bathrooms:</strong> <span class="text-muted">3</span>
                                </div>
                                <div class="mb-3">
                                    <strong>Price:</strong> <span class="text-primary fw-bold">€450,000</span>
                                </div>
                                <p class="text-muted">Beautiful family home in a quiet neighborhood with modern amenities and excellent transport links.</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="contactAboutProperty(${propertyId})">
                            <i class="fas fa-envelope me-2"></i>Contact Agent
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remove existing modal
    const existingModal = document.getElementById('propertyModal');
    if (existingModal) {
        existingModal.remove();
    }

    // Add modal to DOM
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('propertyModal'));
    modal.show();

    // Clean up when modal is hidden
    document.getElementById('propertyModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

/**
 * Handle contact form submission with Ajax
 *
 * This function provides a comprehensive form submission handler that:
 * - Prevents default form submission to avoid page refresh
 * - Shows loading states with visual feedback
 * - Handles both success and error responses gracefully
 * - Provides user-friendly error messages
 * - Supports both modal and inline forms
 * - Implements proper error handling and logging
 *
 * @param {HTMLFormElement} form - The form element to submit
 * @param {boolean} isModal - Whether the form is in a modal (affects close behavior)
 */
function handleContactFormSubmission(form, isModal = false) {
    // Extract form data and prepare for submission
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;

    // Show loading state with spinner animation
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
    submitButton.disabled = true;

    // Clear any previous error messages to start fresh
    clearFormErrors(form);

    // Make Ajax request
    fetch('/contact/submit', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Message sent successfully! We\'ll get back to you soon.', 'success');
            form.reset();

            // Close modal if it's the modal form
            if (isModal) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('contactModal'));
                if (modal) {
                    modal.hide();
                }
            }
        } else {
            // Show validation errors
            if (data.errors) {
                showFormErrors(form, data.errors);
                showNotification('Please fix the errors and try again.', 'error');
            } else {
                showNotification(data.message || 'An error occurred. Please try again.', 'error');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Network error. Please check your connection and try again.', 'error');
    })
    .finally(() => {
        // Reset button
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
}

/**
 * Handle newsletter subscription with Ajax
 */
function handleNewsletterSubmission(form) {
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    const emailInput = form.querySelector('input[name="email"]');

    // Show loading state with transition
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    submitButton.disabled = true;
    emailInput.disabled = true;

    // Add loading class for CSS transitions
    form.classList.add('newsletter-loading');

    // Clear previous error states
    emailInput.classList.remove('is-invalid');

    // Make Ajax request
    fetch('/newsletter/subscribe', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Success state with transition
            form.classList.remove('newsletter-loading');
            form.classList.add('newsletter-success');

            showNotification(data.message || 'Successfully subscribed to newsletter!', 'success');
            form.reset();

            // Remove success class after animation
            setTimeout(() => {
                form.classList.remove('newsletter-success');
            }, 2000);
        } else {
            // Error state
            form.classList.remove('newsletter-loading');

            if (data.errors && data.errors.email) {
                emailInput.classList.add('is-invalid');
                showNotification(data.errors.email, 'error');
            } else {
                showNotification(data.message || 'Subscription failed. Please try again.', 'error');
            }
        }
    })
    .catch(error => {
        console.error('Newsletter subscription error:', error);
        form.classList.remove('newsletter-loading');
        showNotification('Network error. Please check your connection and try again.', 'error');
    })
    .finally(() => {
        // Reset button and input
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
        emailInput.disabled = false;
    });
}

/**
 * Contact about property
 */
function contactAboutProperty(propertyId) {
    // Hide property modal
    const propertyModal = bootstrap.Modal.getInstance(document.getElementById('propertyModal'));
    propertyModal.hide();
    
    // Show contact form or redirect
    showNotification('Redirecting to contact form...', 'info');
    
    setTimeout(() => {
        window.location.href = `/contact?property=${propertyId}`;
    }, 1000);
}

/**
 * Show notification toast
 */
function showNotification(message, type = 'info') {
    const toastHtml = `
        <div class="toast" role="alert">
            <div class="toast-header bg-${type} text-white">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                <strong class="me-auto">Notification</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">${message}</div>
        </div>
    `;

    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '1055';
        toastContainer.style.marginTop = '125px';
        document.body.appendChild(toastContainer);
    }

    toastContainer.insertAdjacentHTML('beforeend', toastHtml);
    const newToast = toastContainer.lastElementChild;
    const bsToast = new bootstrap.Toast(newToast);
    bsToast.show();

    // Remove toast element after it's hidden
    newToast.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

/**
 * Show modal notification for important messages
 */
function showModalNotification(message, type = 'info', title = null) {
    const modal = document.getElementById('notificationModal');
    const modalHeader = document.getElementById('notificationModalHeader');
    const modalIcon = document.getElementById('notificationModalIcon');
    const modalTitle = document.getElementById('notificationModalTitle');
    const modalMessage = document.getElementById('notificationModalMessage');

    // Set colors and icons based on type
    const config = {
        success: {
            headerClass: 'bg-success text-white',
            icon: 'fas fa-check-circle',
            title: title || 'Success',
            closeClass: 'btn-close-white'
        },
        error: {
            headerClass: 'bg-danger text-white',
            icon: 'fas fa-exclamation-circle',
            title: title || 'Error',
            closeClass: 'btn-close-white'
        },
        warning: {
            headerClass: 'bg-warning text-dark',
            icon: 'fas fa-exclamation-triangle',
            title: title || 'Warning',
            closeClass: 'btn-close'
        },
        info: {
            headerClass: 'bg-info text-white',
            icon: 'fas fa-info-circle',
            title: title || 'Information',
            closeClass: 'btn-close-white'
        }
    };

    const typeConfig = config[type] || config.info;

    // Update modal content
    modalHeader.className = `modal-header ${typeConfig.headerClass}`;
    modalIcon.className = `${typeConfig.icon} me-2`;
    modalTitle.textContent = typeConfig.title;
    modalMessage.textContent = message;

    // Update close button
    const closeButton = modalHeader.querySelector('.btn-close');
    closeButton.className = `btn-close ${typeConfig.closeClass}`;

    // Show modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
}

/**
 * Smooth scroll to element
 */
function scrollToElement(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

/**
 * Initialize property search functionality
 */
function initializePropertySearch() {
    const searchForm = document.getElementById('propertySearchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const searchParams = new URLSearchParams();
            
            for (let [key, value] of formData.entries()) {
                if (value) {
                    searchParams.append(key, value);
                }
            }
            
            // Redirect to properties page with search parameters
            window.location.href = `/properties?${searchParams.toString()}`;
        });
    }
}

// Initialize property search when DOM is loaded
document.addEventListener('DOMContentLoaded', initializePropertySearch);

/**
 * Clear form errors
 */
function clearFormErrors(form) {
    // Remove error classes and messages
    const errorElements = form.querySelectorAll('.is-invalid');
    errorElements.forEach(element => {
        element.classList.remove('is-invalid');
    });

    const errorMessages = form.querySelectorAll('.invalid-feedback');
    errorMessages.forEach(message => {
        message.remove();
    });
}

/**
 * Show form errors
 */
function showFormErrors(form, errors) {
    Object.keys(errors).forEach(fieldName => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.classList.add('is-invalid');

            // Create error message element
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = errors[fieldName];

            // Insert error message after the field
            field.parentNode.insertBefore(errorDiv, field.nextSibling);
        }
    });
}

/**
 * Initialize testimonials section functionality
 * Handles staggered animations and enhanced interactions
 */
function initializeTestimonials() {
    const testimonialsSection = document.getElementById('testimonials');
    if (!testimonialsSection) return;

    const testimonialCards = testimonialsSection.querySelectorAll('.testimonial-card');

    // Enhanced intersection observer for testimonials with staggered animation
    const testimonialObserver = new IntersectionObserver(function(entries) {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                // Add staggered delay for each testimonial card
                setTimeout(() => {
                    entry.target.classList.add('animate-fade-in');
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 150); // 150ms delay between each card

                testimonialObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.2,
        rootMargin: '0px 0px -50px 0px'
    });

    // Set initial state and observe testimonial cards
    testimonialCards.forEach((card) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
        testimonialObserver.observe(card);
    });

    // Add enhanced hover effects for testimonial avatars
    testimonialCards.forEach(card => {
        const avatar = card.querySelector('.testimonial-avatar');
        if (avatar) {
            // Add loading state handling
            avatar.addEventListener('load', function() {
                this.style.opacity = '1';
            });

            // Add error handling for avatar images
            avatar.addEventListener('error', function() {
                // Fallback to a default avatar if image fails to load
                this.src = 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="%23e2e8f0"/><circle cx="50" cy="35" r="15" fill="%23cbd5e1"/><path d="M20 80 Q20 65 35 65 L65 65 Q80 65 80 80 Z" fill="%23cbd5e1"/></svg>';
                this.alt = 'Default Avatar';
            });

            // Set initial loading state
            avatar.style.opacity = '0';
            avatar.style.transition = 'opacity 0.3s ease';
        }
    });

    // Add smooth scroll to testimonials section functionality
    const testimonialsLinks = document.querySelectorAll('a[href="#testimonials"]');
    testimonialsLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            scrollToElement('testimonials');
        });
    });

    console.log('Testimonials section initialized successfully');
}

/**
 * Initialize enhanced image handling
 *
 * Provides lazy loading, error handling, and loading states for property images.
 * Improves performance and user experience, especially on mobile devices.
 */
function initializeImageHandling() {
    const propertyImages = document.querySelectorAll('.property-image-hover, .card-img-top');

    // Intersection Observer for lazy loading
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                loadImage(img);
                observer.unobserve(img);
            }
        });
    }, {
        rootMargin: '50px 0px',
        threshold: 0.01
    });

    // Initialize each image
    propertyImages.forEach(img => {
        // Set up lazy loading
        if (img.dataset.src && !img.src) {
            img.dataset.loading = 'true';
            imageObserver.observe(img);
        } else if (img.src) {
            // Handle already loaded images
            setupImageHandlers(img);
        }
    });

    /**
     * Load image with proper error handling
     */
    function loadImage(img) {
        const src = img.dataset.src || img.src;
        if (!src) return;

        // Create a new image to test loading
        const testImg = new Image();

        testImg.onload = function() {
            img.src = src;
            img.dataset.loading = 'false';
            img.dataset.error = 'false';
            setupImageHandlers(img);

            // Add fade-in animation
            img.style.opacity = '0';
            img.style.transition = 'opacity 0.3s ease';
            setTimeout(() => {
                img.style.opacity = '1';
            }, 50);
        };

        testImg.onerror = function() {
            handleImageError(img);
        };

        testImg.src = src;
    }

    /**
     * Handle image loading errors
     */
    function handleImageError(img) {
        img.dataset.loading = 'false';
        img.dataset.error = 'true';

        // Set fallback image
        const fallbackSrc = img.dataset.fallback || '/assets/images/default-property.svg';

        // Try fallback image
        const fallbackImg = new Image();
        fallbackImg.onload = function() {
            img.src = fallbackSrc;
            img.dataset.error = 'false';
        };
        fallbackImg.onerror = function() {
            // If even fallback fails, show placeholder
            img.style.backgroundColor = '#f1f5f9';
            img.alt = 'Image not available';
            console.warn('Failed to load property image and fallback:', img.dataset.src || img.src);
        };
        fallbackImg.src = fallbackSrc;
    }

    /**
     * Set up image event handlers
     */
    function setupImageHandlers(img) {
        // Handle runtime errors
        img.addEventListener('error', function() {
            if (!this.dataset.error || this.dataset.error === 'false') {
                handleImageError(this);
            }
        });

        // Add loading state on src change
        const originalSrc = img.src;
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'src') {
                    if (img.src !== originalSrc && img.src) {
                        img.dataset.loading = 'true';
                        img.addEventListener('load', function() {
                            img.dataset.loading = 'false';
                        }, { once: true });
                    }
                }
            });
        });

        observer.observe(img, { attributes: true });
    }

    // Handle network changes
    if ('connection' in navigator) {
        navigator.connection.addEventListener('change', function() {
            // Retry failed images on network improvement
            if (navigator.connection.effectiveType !== 'slow-2g') {
                const failedImages = document.querySelectorAll('[data-error="true"]');
                failedImages.forEach(img => {
                    if (img.dataset.src) {
                        loadImage(img);
                    }
                });
            }
        });
    }

    console.log('Enhanced image handling initialized for', propertyImages.length, 'images');
}

// Make functions globally available
window.showPropertyModal = showPropertyModal;
window.showNotification = showNotification;
window.scrollToElement = scrollToElement;
window.clearFormErrors = clearFormErrors;
window.showFormErrors = showFormErrors;
window.initializeTestimonials = initializeTestimonials;
window.initializeImageHandling = initializeImageHandling;
