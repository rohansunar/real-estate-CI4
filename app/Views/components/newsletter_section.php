<!-- Newsletter Subscription Section -->
<section class="newsletter-section py-5 bg-gradient-primary text-white position-relative overflow-hidden">
    <!-- Background Pattern -->
    
    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6 text-center">
                <!-- Newsletter Header -->
                <div class="mb-4">
                    <i class="fas fa-envelope-open fs-1 text-white mb-3 d-block"></i>
                    <h2 class="display-5 fw-bold mb-3">Stay Updated with Latest Properties</h2>
                    <p class="fs-5 text-light mb-4">
                        Get exclusive access to new property listings, market insights, and special offers delivered straight to your inbox.
                    </p>
                </div>

                <!-- Newsletter Form -->
                <form id="newsletterSectionForm" action="<?= base_url('newsletter/subscribe') ?>" method="post" class="newsletter-form">
                    <?= csrf_field() ?>
                    <div class="row g-3 justify-content-center">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-0">
                                    <i class="fas fa-envelope text-primary"></i>
                                </span>
                                <input type="email" 
                                       name="email" 
                                       class="form-control border-0 shadow-sm" 
                                       placeholder="Enter your email address" 
                                       required
                                       style="border-radius: 0 0.5rem 0.5rem 0;">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-light w-100 fw-semibold shadow-sm">
                                <i class="fas fa-paper-plane me-2"></i>
                                Subscribe Now
                            </button>
                        </div>
                    </div>
                    
                    <!-- Privacy Notice -->
                    <div class="mt-3">
                        <small class="text-light opacity-75">
                            <i class="fas fa-shield-alt me-1"></i>
                            We respect your privacy. Unsubscribe at any time.
                        </small>
                    </div>
                </form>

                <!-- Benefits List -->
                <div class="row g-3 mt-4 text-start">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <small class="text-light">New Property Alerts</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <small class="text-light">Market Insights</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <small class="text-light">Exclusive Offers</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Newsletter Section Styles */
.newsletter-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.newsletter-form.newsletter-loading .btn {
    pointer-events: none;
}

.newsletter-form.newsletter-success .input-group {
    transform: scale(1.02);
    transition: transform 0.3s ease;
}

.newsletter-section .input-group-text {
    border-right: none !important;
}

.newsletter-section .form-control {
    border-left: none !important;
}

.newsletter-section .form-control:focus {
    box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.25);
    border-color: transparent;
}

.newsletter-section .btn-light:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .newsletter-section .col-md-8,
    .newsletter-section .col-md-4 {
        margin-bottom: 1rem;
    }
    
    .newsletter-section .input-group-lg .form-control,
    .newsletter-section .input-group-lg .input-group-text {
        padding: 0.75rem 1rem;
    }
}
</style>

<script>
// Initialize newsletter section form when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const newsletterSectionForm = document.getElementById('newsletterSectionForm');
    if (newsletterSectionForm) {
        newsletterSectionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Use the existing newsletter submission handler if available
            if (typeof handleNewsletterSubmission === 'function') {
                handleNewsletterSubmission(this);
            } else {
                // Fallback implementation
                handleNewsletterSectionSubmission(this);
            }
        });
    }
});

/**
 * Fallback newsletter submission handler for the newsletter section
 */
function handleNewsletterSectionSubmission(form) {
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    const emailInput = form.querySelector('input[name="email"]');

    // Show loading state
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Subscribing...';
    submitButton.disabled = true;
    emailInput.disabled = true;
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
            form.classList.remove('newsletter-loading');
            form.classList.add('newsletter-success');
            
            // Use global notification function if available
            if (typeof showNotification === 'function') {
                showNotification(data.message || 'Successfully subscribed to newsletter!', 'success');
            } else {
                alert('Successfully subscribed to newsletter!');
            }
            
            form.reset();
            
            setTimeout(() => {
                form.classList.remove('newsletter-success');
            }, 2000);
        } else {
            form.classList.remove('newsletter-loading');
            
            if (data.errors && data.errors.email) {
                emailInput.classList.add('is-invalid');
                if (typeof showNotification === 'function') {
                    showNotification(data.errors.email, 'error');
                } else {
                    alert(data.errors.email);
                }
            } else {
                if (typeof showNotification === 'function') {
                    showNotification(data.message || 'Subscription failed. Please try again.', 'error');
                } else {
                    alert(data.message || 'Subscription failed. Please try again.');
                }
            }
        }
    })
    .catch(error => {
        form.classList.remove('newsletter-loading');
        
        if (typeof showNotification === 'function') {
            showNotification('Network error. Please check your connection and try again.', 'error');
        } else {
            alert('Network error. Please check your connection and try again.');
        }
    })
    .finally(() => {
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
        emailInput.disabled = false;
    });
}
</script>
