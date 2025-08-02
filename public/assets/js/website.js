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
 * @version 2.0
 * @since 2025-08-01
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all website components in order
    initializeNavbar();        // Navigation functionality
    initializeAnimations();    // Scroll animations and transitions
    initializeToasts();        // Toast notification system
    initializePropertyCards(); // Property card interactions
    initializeForms();         // Form validation and Ajax submissions
    initializeHeroCarousel();  // Modern hero carousel functionality

    console.log('Real Estate Website loaded successfully');
});

/**
 * Initialize navbar functionality
 *
 * Handles navbar scroll effects and mobile menu interactions.
 * Adds visual feedback when user scrolls past the hero section.
 */
function initializeNavbar() {
    const navbar = document.querySelector('.navbar');

    if (!navbar) return; // Exit if navbar not found

    // Add scroll effect to navbar for better visual hierarchy
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Close mobile menu when clicking on links (improves UX on mobile)
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (navbarCollapse.classList.contains('show')) {
                const bsCollapse = new bootstrap.Collapse(navbarCollapse);
                bsCollapse.hide();
            }
        });
    });
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

    // Cleanup function to prevent memory leaks
    window.addEventListener('beforeunload', () => {
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
    const animateElements = document.querySelectorAll('.card, .property-card, .hero-content');
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
 */
function initializePropertyCards() {
    // Use event delegation for better performance and memory management
    document.addEventListener('mouseenter', function(e) {
        if (e.target.closest('.property-card, .card')) {
            e.target.closest('.property-card, .card').style.transform = 'translateY(-8px)';
        }
    }, true);

    document.addEventListener('mouseleave', function(e) {
        if (e.target.closest('.property-card, .card')) {
            e.target.closest('.property-card, .card').style.transform = 'translateY(0)';
        }
    }, true);

    // Handle property view button clicks with event delegation
    document.addEventListener('click', function(e) {
        const viewButton = e.target.closest('[data-property-view]');
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

// Make functions globally available
window.showPropertyModal = showPropertyModal;
window.showNotification = showNotification;
window.scrollToElement = scrollToElement;
window.clearFormErrors = clearFormErrors;
window.showFormErrors = showFormErrors;
