<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merish - Premium Salon & Artisan Cafe</title>
    <!-- Load fonts: Playfair Display & Montserrat (Google). Brittany Signature loaded via local @font-face (place woff2 in assets/fonts/) -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS (optional overrides) -->
    <link rel="stylesheet" href="/SIB/PROJECT-APLIN/assets/css/style.css">
</head>
<body>
    <!-- HEADER / NAVIGATION -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom">
        <div class="container-fluid px-4">
            <a href="index.php" class="navbar-brand fw-bold" style="color: #c97fa6; font-size: 24px;">Merish</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php#services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#cafe">The Cafe</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#stylists">Stylists</a></li>
                </ul>
                
                <div class="d-flex gap-2">
                    <a href="index.php?page=login" class="btn btn-outline-secondary">Login / Register</a>
                    <a href="index.php?page=customer&action=appointment" class="btn btn-merish">Book Appointment</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="container-fluid px-0">
            <div class="row align-items-center min-vh-100 g-0">
                <div class="col-lg-6 col-md-12 d-flex align-items-center px-4 px-lg-5">
                    <div class="hero-content">
                        <h1 class="display-4 fw-bold mb-3">
                            Elevate Your Style,
                            <span class="text-merish fst-italic fw-light d-block">Savor the Moment.</span>
                        </h1>
                        
                        <p class="lead text-muted mb-4">
                            Experience the city's first integrated premium hair salon and artisan cafe. 
                            Say goodbye to boring wait times.
                        </p>

                        <div class="d-flex gap-3">
                            <a href="index.php?page=customer&action=appointment" class="btn btn-merish btn-lg">
                                Book an Appointment
                                <span class="ms-2">→</span>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 d-none d-lg-block hero-image-section">
                    <!-- Background image will be set via CSS -->
                </div>
            </div>
        </div>
    </section>

    <!-- CREDENTIALS / BADGES SECTION -->
    <section class="credentials-section py-5 bg-white">
        <div class="container-fluid px-4">
            <div class="row g-4 align-items-center">
                
                <!-- Badge 1 -->
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="credential-badge">
                        <div class="credential-icon mb-3">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#c97fa6" stroke-width="2">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
                        </div>
                        <h4 class="fw-bold mb-2 text-dark">TOP 10 SALON ASIA</h4>
                        <p class="text-muted small">Recognized for excellence</p>
                    </div>
                </div>

                <!-- Badge 2 -->
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="credential-badge">
                        <div class="credential-icon mb-3">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#c97fa6" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                <polyline points="9 12 12 15 15 9"></polyline>
                            </svg>
                        </div>
                        <h4 class="fw-bold mb-2 text-dark">L'ORÃ‰AL PRO CERTIFIED</h4>
                        <p class="text-muted small">Official partner & training</p>
                    </div>
                </div>

                <!-- Badge 3 -->
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="credential-badge">
                        <div class="credential-icon mb-3">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#c97fa6" stroke-width="2">
                                <path d="M14 9V5a3 3 0 0 0-3-3h-4a3 3 0 0 0-3 3v4"></path>
                                <path d="M7 8a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V9a1 1 0 0 0-1-1H7z"></path>
                                <path d="M9 13v4M15 13v4"></path>
                            </svg>
                        </div>
                        <h4 class="fw-bold mb-2 text-dark">10K+ HAPPY CLIENTS</h4>
                        <p class="text-muted small">Trusted by thousands</p>
                    </div>
                </div>

                <!-- Badge 4 -->
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="credential-badge">
                        <div class="credential-icon mb-3">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#c97fa6" stroke-width="2">
                                <path d="M18 8h-1V6c0-2.76-2.24-5-5-5s-5 2.24-5 5v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6-2c1.66 0 3 1.34 3 3v2h-6V9c0-1.66 1.34-3 3-3z"></path>
                                <circle cx="12" cy="16" r="1"></circle>
                            </svg>
                        </div>
                        <h4 class="fw-bold mb-2 text-dark">SCA CERTIFIED BARISTA</h4>
                        <p class="text-muted small">Specialty coffee experts</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- SIGNATURE TREATMENTS SECTION -->
    <section class="signature-treatments py-5 bg-light">
        <div class="container-fluid px-4">
            <!-- Section Title -->
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold mb-4" style="color: var(--text-dark);">Our Signature Treatments</h2>
                
                <!-- Promo Banner -->
                <div class="promo-banner mb-5">
                    <span class="me-2">âœ¨</span>
                    Get 20% Synergy Discount when combining Salon & Cafe orders!
                </div>
            </div>

            <!-- Treatment Cards Grid -->
            <div class="row g-4">
                
                <!-- Treatment Card 1 -->
                <div class="col-lg-4 col-md-6">
                    <div class="treatment-card">
                        <div class="treatment-image" style="background: linear-gradient(135deg, #e8c4d4 0%, #d4a9c3 100%); height: 280px; border-radius: 16px 16px 0 0;">
                            <!-- Placeholder for image -->
                            <div class="d-flex align-items-center justify-content-center h-100 text-white fw-bold">
                                Signature Hair Spa
                            </div>
                        </div>
                        <div class="treatment-details p-4">
                            <h3 class="h5 fw-bold mb-2 text-dark">Signature Hair Spa</h3>
                            <p class="mb-3">
                                <span class="price-label">Rp </span>
                                <span class="price-value">350.000</span>
                            </p>
                            <button class="btn btn-link p-0 fw-bold text-uppercase treatment-btn">
                                Book Now
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Treatment Card 2 -->
                <div class="col-lg-4 col-md-6">
                    <div class="treatment-card">
                        <div class="treatment-image" style="background: linear-gradient(135deg, #e8c4d4 0%, #d4a9c3 100%); height: 280px; border-radius: 16px 16px 0 0;">
                            <!-- Placeholder for image -->
                            <div class="d-flex align-items-center justify-content-center h-100 text-white fw-bold">
                                Luminous Balayage
                            </div>
                        </div>
                        <div class="treatment-details p-4">
                            <h3 class="h5 fw-bold mb-2 text-dark">Luminous Balayage</h3>
                            <p class="mb-3">
                                <span class="price-label">Rp </span>
                                <span class="price-value">850.000</span>
                            </p>
                            <button class="btn btn-link p-0 fw-bold text-uppercase treatment-btn">
                                Book Now
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Treatment Card 3 -->
                <div class="col-lg-4 col-md-6">
                    <div class="treatment-card">
                        <div class="treatment-image" style="background: linear-gradient(135deg, #e8c4d4 0%, #d4a9c3 100%); height: 280px; border-radius: 16px 16px 0 0;">
                            <!-- Placeholder for image -->
                            <div class="d-flex align-items-center justify-content-center h-100 text-white fw-bold">
                                Premium Men Cut
                            </div>
                        </div>
                        <div class="treatment-details p-4">
                            <h3 class="h5 fw-bold mb-2 text-dark">Premium Men Cut</h3>
                            <p class="mb-3">
                                <span class="price-label">Rp </span>
                                <span class="price-value">250.000</span>
                            </p>
                            <button class="btn btn-link p-0 fw-bold text-uppercase treatment-btn">
                                Book Now
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- TESTIMONIALS / CLIENT REVIEWS SECTION -->
    <section class="testimonials-section py-5">
        <div class="container-fluid px-4">
            <!-- Section Title -->
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold" style="color: white;">Loved by Our Clients</h2>
            </div>

            <!-- Testimonials Row - Bootstrap Cards -->
            <div class="row g-4 justify-content-center">
                
                <!-- Testimonial Card 1 -->
                <div class="col-lg-4 col-md-6">
                    <div class="card testimonial-card border-0">
                        <div class="card-body">
                            <!-- Stars -->
                            <div class="stars mb-3">
                                <span class="star">â˜…</span>
                                <span class="star">â˜…</span>
                                <span class="star">â˜…</span>
                                <span class="star">â˜…</span>
                                <span class="star">â˜…</span>
                            </div>
                            
                            <!-- Review Text (Empty) -->
                            <p class="testimonial-text mb-4">
                                <!-- Review content will be added here -->
                            </p>
                            
                            <!-- Author -->
                            <p class="testimonial-author mb-0">- Jessica M.</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial Card 2 -->
                <div class="col-lg-4 col-md-6">
                    <div class="card testimonial-card border-0">
                        <div class="card-body">
                            <!-- Stars -->
                            <div class="stars mb-3">
                                <span class="star">â˜…</span>
                                <span class="star">â˜…</span>
                                <span class="star">â˜…</span>
                                <span class="star">â˜…</span>
                                <span class="star">â˜…</span>
                            </div>
                            
                            <!-- Review Text (Empty) -->
                            <p class="testimonial-text mb-4">
                                <!-- Review content will be added here -->
                            </p>
                            
                            <!-- Author -->
                            <p class="testimonial-author mb-0">- Amanda R.</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial Card 3 -->
                <div class="col-lg-4 col-md-6">
                    <div class="card testimonial-card border-0">
                        <div class="card-body">
                            <!-- Stars -->
                            <div class="stars mb-3">
                                <span class="star">â˜…</span>
                                <span class="star">â˜…</span>
                                <span class="star">â˜…</span>
                                <span class="star">â˜…</span>
                                <span class="star">â˜…</span>
                            </div>
                            
                            <!-- Review Text (Empty) -->
                            <p class="testimonial-text mb-4">
                                <!-- Review content will be added here -->
                            </p>
                            
                            <!-- Author -->
                            <p class="testimonial-author mb-0">- Kevin W.</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Scroll Indicator -->
            <div class="scroll-indicator mt-5">
                <div class="scroll-track">
                    <div class="scroll-thumb"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- OUR TOP PERFORMERS SECTION -->
    <section class="top-performers py-5 bg-light">
        <div class="container-fluid px-4">
            <!-- Section Title -->
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold mb-3" style="color: var(--text-dark);">Our Top Performers</h2>
                <p class="lead text-muted">Meet the artists behind the masterpieces.</p>
            </div>

            <!-- Performers Grid -->
            <div class="row g-4 justify-content-center">
                
                <!-- Performer 1 -->
                <div class="col-lg-4 col-md-6 text-center">
                    <div class="performer-card">
                        <!-- Circular Image -->
                        <div class="performer-image mb-4">
                            <div class="image-placeholder">
                                <!-- Image placeholder with circular frame -->
                                <svg viewBox="0 0 200 200" class="placeholder-svg">
                                    <circle cx="100" cy="100" r="95" fill="#e8c4d4" stroke="#c97fa6" stroke-width="2"/>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Performer Name -->
                        <h3 class="h4 fw-bold mb-2 text-dark">Siska</h3>
                        
                        <!-- Role -->
                        <p class="text-merish fw-semibold mb-3">VIP Stylist</p>
                        
                        <!-- Stars -->
                        <div class="stars justify-content-center">
                            <span class="star">â˜…</span>
                            <span class="star">â˜…</span>
                            <span class="star">â˜…</span>
                            <span class="star">â˜…</span>
                            <span class="star">â˜…</span>
                        </div>
                    </div>
                </div>

                <!-- Performer 2 -->
                <div class="col-lg-4 col-md-6 text-center">
                    <div class="performer-card">
                        <!-- Circular Image -->
                        <div class="performer-image mb-4">
                            <div class="image-placeholder">
                                <!-- Image placeholder with circular frame -->
                                <svg viewBox="0 0 200 200" class="placeholder-svg">
                                    <circle cx="100" cy="100" r="95" fill="#e8c4d4" stroke="#c97fa6" stroke-width="2"/>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Performer Name -->
                        <h3 class="h4 fw-bold mb-2 text-dark">Budi</h3>
                        
                        <!-- Role -->
                        <p class="text-merish fw-semibold mb-3">Master Barber</p>
                        
                        <!-- Stars -->
                        <div class="stars justify-content-center">
                            <span class="star">â˜…</span>
                            <span class="star">â˜…</span>
                            <span class="star">â˜…</span>
                            <span class="star">â˜…</span>
                            <span class="star">â˜…</span>
                        </div>
                    </div>
                </div>

                <!-- Performer 3 -->
                <div class="col-lg-4 col-md-6 text-center">
                    <div class="performer-card">
                        <!-- Circular Image -->
                        <div class="performer-image mb-4">
                            <div class="image-placeholder">
                                <!-- Image placeholder with circular frame -->
                                <svg viewBox="0 0 200 200" class="placeholder-svg">
                                    <circle cx="100" cy="100" r="95" fill="#e8c4d4" stroke="#c97fa6" stroke-width="2"/>
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Performer Name -->
                        <h3 class="h4 fw-bold mb-2 text-dark">Rina</h3>
                        
                        <!-- Role -->
                        <p class="text-merish fw-semibold mb-3">Senior Beautician</p>
                        
                        <!-- Stars -->
                        <div class="stars justify-content-center">
                            <span class="star">â˜…</span>
                            <span class="star">â˜…</span>
                            <span class="star">â˜…</span>
                            <span class="star">â˜…</span>
                            <span class="star">â˜…</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer-merish py-5">
        <div class="container-fluid px-4">
            <div class="row text-light">
                <div class="col-md-5 mb-4">
                    <h3 class="fw-bold mb-3" style="font-family: 'Playfair Display', serif;">Merish</h3>
                    <p class="text-muted">Elevating your style while satisfying your palate.<br>Merish is the perfect blend of premium beauty treatments and artisan culinary delights.</p>
                    <div class="mt-3 d-flex gap-2">
                        <a href="#" class="social-btn d-inline-flex align-items-center justify-content-center" aria-label="Instagram">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="4"></rect><circle cx="12" cy="12" r="3"></circle></svg>
                        </a>
                        <a href="#" class="social-btn d-inline-flex align-items-center justify-content-center" aria-label="Twitter">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53A4.48 4.48 0 0 0 22.43 1s-4 2-6 2a4.5 4.5 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 2s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>
                        </a>
                        <a href="#" class="social-btn d-inline-flex align-items-center justify-content-center" aria-label="Facebook">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 2h-3a4 4 0 0 0-4 4v3H8v4h3v8h4v-8h3l1-4h-4V6a1 1 0 0 1 1-1h3z"></path></svg>
                        </a>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold mb-3">Visit Us</h5>
                    <p class="text-muted mb-0"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="me-2"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0z"></path><circle cx="12" cy="10" r="2"></circle></svg>123 Luxury Avenue,<br>Sudirman Central District,<br>Jakarta 12190</p>
                </div>

                <div class="col-md-3 mb-4">
                    <h5 class="fw-bold mb-3">Hours</h5>
                    <p class="text-muted mb-0"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="me-2"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v6l4 2"></path></svg><strong>Mon - Sun</strong><br>09:00 AM - 09:00 PM</p>
                </div>
            </div>

            <hr class="border-secondary">

            <div class="d-flex justify-content-between py-3 text-muted small">
                <div>Â© 2026 Merish Salon & Cafe. All rights reserved.</div>
                <div>
                    <a href="#" class="text-muted me-3">Staff Login</a>
                    <a href="#" class="text-muted">Privacy Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/SIB/PROJECT-APLIN/assets/js/script.js"></script>
</body>
</html>


