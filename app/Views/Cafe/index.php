<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Merish The Cafe - Artisan Beverages & Pastries. Premium coffee and tea experience.">
    <title>The Cafe - Merish Premium Salon & Artisan Cafe</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3.0 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/SIB/PROJECT-APLIN/assets/css/style.css">
    
    <style>
        /* Cafe Page Custom Styles */
        .cafe-greeting {
            background: linear-gradient(135deg, #f5f3f0 0%, #e8ddd8 100%);
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 40px;
        }

        .greeting-icon {
            font-size: 32px;
            margin-right: 15px;
        }

        .greeting-text {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            color: #8b7f7f;
            margin-bottom: 5px;
        }

        .greeting-location {
            font-size: 12px;
            color: #8b7f7f;
        }

        .cafe-hero {
            text-align: center;
            margin-bottom: 50px;
        }

        .cafe-title {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            color: #8b7f7f;
            margin-bottom: 15px;
            font-weight: 400;
        }

        .cafe-subtitle {
            font-size: 16px;
            color: #7a7a7a;
            max-width: 600px;
            margin: 0 auto 40px;
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
            font-size: 11px;
            font-weight: 700;
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

        .menu-item-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            border: 1px solid #e8ddd8;
        }

        .menu-item-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        .menu-image {
            width: 100%;
            height: 220px;
            background: linear-gradient(135deg, #e8ddd8 0%, #d4c8c0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8b7f7f;
            font-weight: 600;
            text-align: center;
            padding: 20px;
        }

        .menu-info {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .menu-name {
            font-family: 'Playfair Display', serif;
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .menu-description {
            font-size: 12px;
            color: #999;
            line-height: 1.6;
            margin-bottom: 12px;
        }

        .menu-price {
            font-size: 14px;
            color: #8b7f7f;
            font-weight: 700;
        }

        .order-section {
            text-align: center;
            margin: 50px 0;
        }

        .order-btn {
            background: #8b7f7f;
            color: white;
            padding: 14px 40px;
            border: none;
            border-radius: 4px;
            font-family: 'Montserrat', sans-serif;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .order-btn:hover {
            background: #7a6f6f;
        }

        .order-btn-arrow {
            margin-left: 10px;
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
                        <a class="nav-link text-uppercase fw-500" style="font-size: 13px; letter-spacing: 1px; color: #666;" href="index.php?page=cafe">THE CAFE</a>
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
         CAFE GREETING SECTION
         ============================================ -->
    <section class="py-5">
        <div class="container-xl px-4">
            <div class="cafe-greeting">
                <div class="d-flex align-items-start">
                    <div class="greeting-icon">👋</div>
                    <div>
                        <p class="greeting-text">Halo, Guest!</p>
                        <p class="greeting-location">📍 Ungu Avenue, Jakarta Selatan 2 Jalan</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         CAFE HERO SECTION
         ============================================ -->
    <section class="cafe-hero container-xl px-4 py-5">
        <h1 class="cafe-title">The Cafe Menu</h1>
        <p class="cafe-subtitle">
            Indulge in our curated selection of artisanal beverages and delicate pastries while you enjoy your treatment.
        </p>
    </section>

    <!-- ============================================
         CATEGORY FILTERS
         ============================================ -->
    <section class="py-4" style="background-color: white;">
        <div class="container-xl px-4">
            <div class="category-filters">
                <button class="category-btn active" data-category="all">ALL</button>
                <button class="category-btn" data-category="kopi">KOPI</button>
                <button class="category-btn" data-category="teh">TEH</button>
                <button class="category-btn" data-category="pastry">PASTRY</button>
                <button class="category-btn" data-category="non-coffee">NON-COFFEE</button>
            </div>
        </div>
    </section>

    <!-- ============================================
         MENU ITEMS GRID
         ============================================ -->
    <section class="py-5" style="background-color: #fafafa;">
        <div class="container-xl px-4">
            <div class="row g-4">
                <!-- Menu Item 1: Kopi -->
                <div class="col-lg-4 col-md-6" data-category="kopi all">
                    <div class="menu-item-card">
                        <div class="menu-image">
                            ☕ Iced Rose Latte
                        </div>
                        <div class="menu-info">
                            <div>
                                <h3 class="menu-name">Iced Rose Latte</h3>
                                <p class="menu-description">Signature espresso blend with delicate rose syrup, creamy milk, and organic dried rose.</p>
                            </div>
                            <p class="menu-price">Rp 65.000</p>
                        </div>
                    </div>
                </div>

                <!-- Menu Item 2: Kopi -->
                <div class="col-lg-4 col-md-6" data-category="kopi all">
                    <div class="menu-item-card">
                        <div class="menu-image">
                            ☕ Classic Cappuccino
                        </div>
                        <div class="menu-info">
                            <div>
                                <h3 class="menu-name">Classic Cappuccino</h3>
                                <p class="menu-description">Rich double espresso balanced with equal parts creamed milk and a thick layer of foam.</p>
                            </div>
                            <p class="menu-price">Rp 70.000</p>
                        </div>
                    </div>
                </div>

                <!-- Menu Item 3: Teh -->
                <div class="col-lg-4 col-md-6" data-category="teh all">
                    <div class="menu-item-card">
                        <div class="menu-image">
                            🫖 Earl Grey Lavender
                        </div>
                        <div class="menu-info">
                            <div>
                                <h3 class="menu-name">Earl Grey Lavender</h3>
                                <p class="menu-description">Premium bergamot infused black tea steeped with culinary lavender buds for a delicate aroma.</p>
                            </div>
                            <p class="menu-price">Rp 42.000</p>
                        </div>
                    </div>
                </div>

                <!-- Menu Item 4: Pastry -->
                <div class="col-lg-4 col-md-6" data-category="pastry all">
                    <div class="menu-item-card">
                        <div class="menu-image">
                            🥐 Almond Croissant
                        </div>
                        <div class="menu-info">
                            <div>
                                <h3 class="menu-name">Almond Croissant</h3>
                                <p class="menu-description">Flaky laminated butter croissant filled with rich almond cream, topped with sliced almonds.</p>
                            </div>
                            <p class="menu-price">Rp 35.000</p>
                        </div>
                    </div>
                </div>

                <!-- Menu Item 5: Non-Coffee -->
                <div class="col-lg-4 col-md-6" data-category="non-coffee all">
                    <div class="menu-item-card">
                        <div class="menu-image">
                            🥛 Vanilla Oat Milk Latte
                        </div>
                        <div class="menu-info">
                            <div>
                                <h3 class="menu-name">Vanilla Oat Milk Latte</h3>
                                <p class="menu-description">Creamy oat milk steamed with Madagascar vanilla extract for a smooth, dairy-free indulgence.</p>
                            </div>
                            <p class="menu-price">Rp 58.000</p>
                        </div>
                    </div>
                </div>

                <!-- Menu Item 6: Pastry -->
                <div class="col-lg-4 col-md-6" data-category="pastry all">
                    <div class="menu-item-card">
                        <div class="menu-image">
                            🍰 Matcha Cheesecake
                        </div>
                        <div class="menu-info">
                            <div>
                                <h3 class="menu-name">Matcha Cheesecake</h3>
                                <p class="menu-description">Premium Japanese matcha powder swirled into a luxurious cheesecake with a buttery graham crust.</p>
                            </div>
                            <p class="menu-price">Rp 55.000</p>
                        </div>
                    </div>
                </div>

                <!-- Menu Item 7: Kopi -->
                <div class="col-lg-4 col-md-6" data-category="kopi all">
                    <div class="menu-item-card">
                        <div class="menu-image">
                            ☕ Cortado
                        </div>
                        <div class="menu-info">
                            <div>
                                <h3 class="menu-name">Cortado</h3>
                                <p class="menu-description">Balanced double espresso with equal ratio of steamed milk, creating the perfect harmony of flavors.</p>
                            </div>
                            <p class="menu-price">Rp 48.000</p>
                        </div>
                    </div>
                </div>

                <!-- Menu Item 8: Teh -->
                <div class="col-lg-4 col-md-6" data-category="teh all">
                    <div class="menu-item-card">
                        <div class="menu-image">
                            🫖 Chamomile Honey
                        </div>
                        <div class="menu-info">
                            <div>
                                <h3 class="menu-name">Chamomile Honey</h3>
                                <p class="menu-description">Soothing chamomile flowers steeped with organic wildflower honey and a hint of citrus zest.</p>
                            </div>
                            <p class="menu-price">Rp 40.000</p>
                        </div>
                    </div>
                </div>

                <!-- Menu Item 9: Non-Coffee -->
                <div class="col-lg-4 col-md-6" data-category="non-coffee all">
                    <div class="menu-item-card">
                        <div class="menu-image">
                            🍫 Hot Chocolate Deluxe
                        </div>
                        <div class="menu-info">
                            <div>
                                <h3 class="menu-name">Hot Chocolate Deluxe</h3>
                                <p class="menu-description">Velvety Belgian chocolate with premium dark cocoa, topped with house-made whipped cream and cocoa powder.</p>
                            </div>
                            <p class="menu-price">Rp 62.000</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         ORDER SECTION
         ============================================ -->
    <section class="order-section py-5">
        <div class="container-xl px-4">
            <button class="order-btn">
                ORDER NOW <span class="order-btn-arrow">→</span>
            </button>
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
                
                // Filter menu items
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

        // Order button functionality
        document.querySelector('.order-btn').addEventListener('click', function() {
            // Can be linked to cart/order page later
            alert('Order feature coming soon!');
        });
    </script>
</body>
</html>
