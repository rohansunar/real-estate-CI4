<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="position-relative py-5 bg-primary text-white" style="margin-top: 76px;">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">About Our Real Estate Company</h1>
                <p class="fs-5 mb-4">
                    We are a leading real estate company in Siliguri and surrounding areas, dedicated to helping you find your perfect home or investment property.
                </p>
            </div>
            <div class="col-lg-6">
                <div class="text-center">
                    <i class="fas fa-building display-1 text-white opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Content -->
<section class="py-5">
    <div class="container py-5">
        <div class="row g-5">
            <div class="col-lg-6">
                <h2 class="h2 fw-bold text-dark mb-4">Our Story</h2>
                <p class="text-muted mb-4">
                    Founded with a vision to transform the real estate landscape in Siliguri, we have been serving the community for years. Our deep understanding of the local market, combined with our commitment to excellence, makes us the preferred choice for property buyers and sellers.
                </p>
                <p class="text-muted mb-4">
                    From the bustling areas of Champasari and Pradhan Nagar to the serene locations of Bagdogra and Matigara, we have extensive knowledge of every neighborhood in and around Siliguri.
                </p>
                <div class="row g-4 mt-4">
                    <div class="col-6">
                        <div class="text-center">
                            <div class="display-5 fw-bold text-primary">500+</div>
                            <div class="small text-muted">Properties Sold</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <div class="display-5 fw-bold text-primary">1000+</div>
                            <div class="small text-muted">Happy Clients</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="bg-light rounded-4 p-4">
                    <h3 class="h4 fw-bold text-dark mb-4">Why Choose Us?</h3>
                    <div class="d-flex align-items-start mb-3">
                        <i class="fas fa-check-circle text-primary me-3 mt-1"></i>
                        <div>
                            <h5 class="fw-semibold mb-1">Local Expertise</h5>
                            <p class="text-muted mb-0">Deep knowledge of Siliguri and surrounding areas</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-3">
                        <i class="fas fa-check-circle text-primary me-3 mt-1"></i>
                        <div>
                            <h5 class="fw-semibold mb-1">Personalized Service</h5>
                            <p class="text-muted mb-0">Tailored solutions for every client's unique needs</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-3">
                        <i class="fas fa-check-circle text-primary me-3 mt-1"></i>
                        <div>
                            <h5 class="fw-semibold mb-1">Transparent Process</h5>
                            <p class="text-muted mb-0">Clear communication and honest dealings</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start">
                        <i class="fas fa-check-circle text-primary me-3 mt-1"></i>
                        <div>
                            <h5 class="fw-semibold mb-1">After-Sales Support</h5>
                            <p class="text-muted mb-0">Continued assistance even after the transaction</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="py-5 bg-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold text-dark mb-4">Meet Our Team</h2>
            <p class="fs-5 text-muted mx-auto" style="max-width: 600px;">
                Our experienced team of real estate professionals is here to guide you through every step of your property journey.
            </p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm text-center">
                    <div class="card-body p-4">
                        <div class="bg-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-user text-white fs-2"></i>
                        </div>
                        <h5 class="card-title fw-bold">Rajesh Kumar</h5>
                        <p class="text-muted mb-3">Senior Property Consultant</p>
                        <p class="card-text small">
                            With over 10 years of experience in Siliguri real estate market, Rajesh specializes in residential properties.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm text-center">
                    <div class="card-body p-4">
                        <div class="bg-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-user text-white fs-2"></i>
                        </div>
                        <h5 class="card-title fw-bold">Priya Sharma</h5>
                        <p class="text-muted mb-3">Commercial Property Expert</p>
                        <p class="card-text small">
                            Priya has extensive knowledge of commercial properties in Jalpaiguri and surrounding business districts.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm text-center">
                    <div class="card-body p-4">
                        <div class="bg-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-user text-white fs-2"></i>
                        </div>
                        <h5 class="card-title fw-bold">Amit Das</h5>
                        <p class="text-muted mb-3">Investment Advisor</p>
                        <p class="card-text small">
                            Amit helps clients make smart investment decisions in the growing real estate market of North Bengal.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact CTA -->
<section class="py-5 bg-primary text-white">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="h2 fw-bold mb-3">Ready to Find Your Dream Property?</h2>
                <p class="fs-5 mb-0">
                    Let our experienced team help you navigate the Siliguri real estate market and find the perfect property for your needs.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <button type="button" class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#contactModal">
                    <i class="fas fa-phone me-2"></i>Contact Us Today
                </button>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Add any page-specific JavaScript here
document.addEventListener('DOMContentLoaded', function() {
    // Animate cards on scroll
    const cards = document.querySelectorAll('.card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.transform = 'translateY(0)';
                entry.target.style.opacity = '1';
            }
        });
    }, { threshold: 0.1 });
    
    cards.forEach(card => {
        card.style.transform = 'translateY(20px)';
        card.style.opacity = '0';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });
});
</script>
<?= $this->endSection() ?>
