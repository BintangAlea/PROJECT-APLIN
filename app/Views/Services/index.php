<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Merish Services - Premium Salon & Artisan Cafe. Discover our signature treatments.">
    <title>Services - Merish Premium Salon & Artisan Cafe</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3.0 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/SIB/PROJECT-APLIN/assets/css/style.css">
    
    <style>
        /* Services Page Custom Styles */
        .services-hero {
            background: linear-gradient(rgba(0,0,0,0.15), rgba(0,0,0,0.15)), 
                        url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 1200 400%22%3E%3Crect fill=%22%23f5f3f0%22 width=%221200%22 height=%22400%22/%3E%3Crect fill=%22%23e8ddd8%22 x=%220%22 y=%220%22 width=%22300%22 height=%22400%22 opacity=%220.5%22/%3E%3C/svg%3E');
            background-attachment: fixed;
            background-position: center;
            background-size: cover;
            min-height: 350px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .category-filters {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 50px;
        }

        .category-btn {
            padding: 8px 20px;
            border: 1px solid #8b7f7f;
            background: transparent;
            color: #8b7f7f;
            font-family: 'Montserrat', sans-serif;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 4px;
        }

        .category-btn:hover,
        .category-btn.active {
            background-color: #8b7f7f;
            color: white;
            border-color: #8b7f7f;
        }

        .service-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .service-image {
            width: 100%;
            height: 280px;
            background: linear-gradient(135deg, #e8ddd8 0%, #d4c8c0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8b7f7f;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
            padding: 20px;
        }

        .service-info {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .service-name {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .service-price {
            font-size: 16px;
            color: #8b7f7f;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .service-btn {
            align-self: flex-start;
            background: #8b7f7f;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-family: 'Montserrat', sans-serif;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .service-btn:hover {
            background: #7a6f6f;
        }

        .expert-card {
            text-align: center;
            padding: 30px;
        }

        .expert-photo {
            width: 200px;
            height: 200px;
            margin: 0 auto 20px;
            border-radius: 8px;
            background: linear-gradient(135deg, #d4c8c0 0%, #c5b8af 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8b7f7f;
            font-weight: 600;
            overflow: hidden;
        }

        .expert-name {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            color: #333;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .expert-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 11px;
            color: #8b7f7f;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .expert-rating {
            margin-bottom: 15px;
        }

        .expert-rating span {
            color: #8b7f7f;
            font-size: 14px;
        }

        .expert-description {
            font-size: 13px;
            color: #666;
            line-height: 1.8;
        }

        .promo-badge {
            display: inline-block;
            background: rgba(139, 127, 127, 0.1);
            padding: 15px 25px;
            border-radius: 4px;
            font-size: 12px;
            color: #8b7f7f;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <!-- ============================================
         HEADER / NAVIGATION
         ============================================ -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top border-bottom">
        <div class="container-xl">
            <a href="index.php" class="navbar-brand fw-normal" style="font-family: 'Playfair Display', serif; font-size: 24px; letter-spacing: 2px; color: #5a5a5a;">Merish</a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto gap-4">
                    <li class="nav-item">
                        <a class="nav-link text-uppercase fw-500" style="font-size: 13px; letter-spacing: 1px; color: #666;" href="index.php">HOME</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-uppercase fw-500" style="font-size: 13px; letter-spacing: 1px; color: #666;" href="#cafe">THE CAFE</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-uppercase fw-500" style="font-size: 13px; letter-spacing: 1px; color: #666;" href="index.php?page=services">SERVICES</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-uppercase fw-500" style="font-size: 13px; letter-spacing: 1px; color: #666;" href="index.php?page=booking&step=1">BOOK NOW</a>
                    </li>
                </ul>
                
                <div class="ms-lg-auto">
                    <a href="index.php?page=login" class="text-uppercase fw-500" style="font-size: 13px; letter-spacing: 1px; color: #666; text-decoration: none;">SIGN IN</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- ============================================
         SERVICES HERO SECTION
         ============================================ -->
    <section class="services-hero">
        <div class="container-xl text-center px-4">
            <h1 class="display-4 fw-normal" style="font-family: 'Playfair Display', serif; color: #8b7f7f; font-size: 48px; letter-spacing: -1px;">
                Our Signature Treatments
            </h1>
            <p style="font-size: 16px; color: #7a7a7a; margin-top: 15px; max-width: 600px; margin-left: auto; margin-right: auto;">
                Curated services designed to elevate your personal style with professional precision.
            </p>
        </div>
    </section>

    <!-- ============================================
         PROMO BANNER
         ============================================ -->
    <section class="py-5" style="background-color: white;">
        <div class="container-xl text-center px-4">
            <div class="promo-badge">
                ⭐ ENJOY A 25% SYNERGY DISCOUNT ON COMBINED SERVICES
            </div>
        </div>
    </section>

    <!-- ============================================
         CATEGORY FILTERS
         ============================================ -->
    <section class="py-5" style="background-color: white;">
        <div class="container-xl px-4">
            <div class="category-filters">
                <button class="category-btn active" data-category="all">ALL</button>
                <button class="category-btn" data-category="hair">HAIR</button>
                <button class="category-btn" data-category="nails">NAILS</button>
                <button class="category-btn" data-category="lashes">LASHES</button>
                <button class="category-btn" data-category="wax">WAX & EYEBROWS</button>
            </div>
        </div>
    </section>

    <!-- ============================================
         SERVICES GRID
         ============================================ -->
    <section class="py-5" style="background-color: #fafafa;">
        <div class="container-xl px-4">
            <div class="row g-4">
                <!-- Service Card 1: Hair -->
                <div class="col-lg-4 col-md-6" data-category="hair all">
                    <div class="service-card">
                        <div class="service-image">
                            💇‍♀️ Signature Balayage
                        </div>
                        <div class="service-info">
                            <div>
                                <h3 class="service-name">Signature Balayage</h3>
                                <p class="service-price">Starting from Rp 850.000</p>
                            </div>
                            <button class="service-btn" onclick="bookService('Signature Balayage')">BOOK NOW</button>
                        </div>
                    </div>
                </div>

                <!-- Service Card 2: Nails -->
                <div class="col-lg-4 col-md-6" data-category="nails all">
                    <div class="service-card">
                        <div class="service-image">
                            💅 Editorial Manicure
                        </div>
                        <div class="service-info">
                            <div>
                                <h3 class="service-name">Editorial Manicure</h3>
                                <p class="service-price">Starting from Rp 350.000</p>
                            </div>
                            <button class="service-btn" onclick="bookService('Editorial Manicure')">BOOK NOW</button>
                        </div>
                    </div>
                </div>

                <!-- Service Card 3: Lashes -->
                <div class="col-lg-4 col-md-6" data-category="lashes all">
                    <div class="service-card">
                        <div class="service-image">
                            ✨ Volume Lash Extensions
                        </div>
                        <div class="service-info">
                            <div>
                                <h3 class="service-name">Volume Lash Extensions</h3>
                                <p class="service-price">Starting from Rp 550.000</p>
                            </div>
                            <button class="service-btn" onclick="bookService('Volume Lash Extensions')">BOOK NOW</button>
                        </div>
                    </div>
                </div>

                <!-- Service Card 4: Hair -->
                <div class="col-lg-4 col-md-6" data-category="hair all">
                    <div class="service-card">
                        <div class="service-image">
                            ✂️ Premium Hair Cut
                        </div>
                        <div class="service-info">
                            <div>
                                <h3 class="service-name">Premium Hair Cut</h3>
                                <p class="service-price">Starting from Rp 250.000</p>
                            </div>
                            <button class="service-btn" onclick="bookService('Premium Hair Cut')">BOOK NOW</button>
                        </div>
                    </div>
                </div>

                <!-- Service Card 5: Wax & Eyebrows -->
                <div class="col-lg-4 col-md-6" data-category="wax all">
                    <div class="service-card">
                        <div class="service-image">
                            🎯 Eyebrow Design & Tint
                        </div>
                        <div class="service-info">
                            <div>
                                <h3 class="service-name">Eyebrow Design & Tint</h3>
                                <p class="service-price">Starting from Rp 150.000</p>
                            </div>
                            <button class="service-btn" onclick="bookService('Eyebrow Design & Tint')">BOOK NOW</button>
                        </div>
                    </div>
                </div>

                <!-- Service Card 6: Nails -->
                <div class="col-lg-4 col-md-6" data-category="nails all">
                    <div class="service-card">
                        <div class="service-image">
                            💄 Gel Pedicure
                        </div>
                        <div class="service-info">
                            <div>
                                <h3 class="service-name">Gel Pedicure</h3>
                                <p class="service-price">Starting from Rp 300.000</p>
                            </div>
                            <button class="service-btn" onclick="bookService('Gel Pedicure')">BOOK NOW</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         EXPERTS SECTION
         ============================================ -->
    <section class="py-5" style="background-color: white;">
        <div class="container-xl px-4">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-normal" style="font-family: 'Playfair Display', serif; color: #8b7f7f; font-size: 42px; font-weight: 400;">
                    Meet the Experts
                </h2>
                <p style="font-size: 16px; color: #7a7a7a; margin-top: 10px;">
                    Our top-rated professionals dedicated to elevating your experience.
                </p>
            </div>

            <div class="row g-4 mt-4">
                <!-- Expert 1 -->
                <div class="col-lg-4 col-md-6">
                    <div class="expert-card">
                        <div class="expert-photo">
                            👩‍💼
                        </div>
                        <h3 class="expert-name">Elena R.</h3>
                        <p class="expert-title">COLOR DIRECTOR</p>
                        <div class="expert-rating">
                            <span>⭐⭐⭐⭐⭐</span>
                        </div>
                        <p class="expert-description">
                            Master of dimensional color and balayage techniques with over a decade of high-fashion experience.
                        </p>
                    </div>
                </div>

                <!-- Expert 2 -->
                <div class="col-lg-4 col-md-6">
                    <div class="expert-card">
                        <div class="expert-photo">
                            👨‍💼
                        </div>
                        <h3 class="expert-name">Marcus T.</h3>
                        <p class="expert-title">MASTER CUTTER</p>
                        <div class="expert-rating">
                            <span>⭐⭐⭐⭐⭐</span>
                        </div>
                        <p class="expert-description">
                            Precision cutting specialist known for creating effortless, structured silhouettes tailored to each individual.
                        </p>
                    </div>
                </div>

                <!-- Expert 3 -->
                <div class="col-lg-4 col-md-6">
                    <div class="expert-card">
                        <div class="expert-photo">
                            👩‍💼
                        </div>
                        <h3 class="expert-name">Chloe M.</h3>
                        <p class="expert-title">SENIOR ESTHETICIAN</p>
                        <div class="expert-rating">
                            <span>⭐⭐⭐⭐⭐</span>
                        </div>
                        <p class="expert-description">
                            Specializing in advanced skincare treatments and meticulous brow architecture to enhance natural beauty.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         FOOTER
         ============================================ -->
    <footer style="background-color: #f5f3f0; border-top: 1px solid #e8ddd8;">
        <div class="container-xl px-4 py-5">
            <div class="row align-items-start mb-5 pb-5" style="border-bottom: 1px solid #e8ddd8;">
                <!-- Footer Left -->
                <div class="col-lg-6">
                    <h3 style="font-family: 'Playfair Display', serif; font-size: 28px; color: #8b7f7f; font-weight: 400; letter-spacing: 1px;">Merish</h3>
                    <p style="font-size: 12px; color: #999; margin-top: 10px;">© 2024 Merish Beauty & Cafe. All rights reserved.</p>
                </div>
                
                <!-- Footer Right - Links -->
                <div class="col-lg-6 text-lg-end">
                    <div class="d-flex justify-content-lg-end gap-4 flex-wrap">
                        <a href="#" style="font-size: 12px; color: #666; text-decoration: none; font-weight: 500;">Contact</a>
                        <a href="#" style="font-size: 12px; color: #666; text-decoration: none; font-weight: 500;">Location</a>
                        <a href="#" style="font-size: 12px; color: #666; text-decoration: none; font-weight: 500;">Instagram</a>
                        <a href="#" style="font-size: 12px; color: #666; text-decoration: none; font-weight: 500;">Pinterest</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3.0 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Category Filter Functionality
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Update active button
                document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Filter services
                const category = this.getAttribute('data-category');
                document.querySelectorAll('[data-category]').forEach(card => {
                    const categories = card.getAttribute('data-category').split(' ');
                    if (categories.includes(category)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Book Service Function
        function bookService(serviceName) {
            // Redirect to booking page with service selected
            window.location.href = 'index.php?page=booking&step=1&service=' + encodeURIComponent(serviceName);
        }
    </script>
</body>
</html>
