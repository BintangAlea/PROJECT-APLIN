<?php 
// QR Order - Menu Selection
// Display cafe menu items for guest to select
$seatName = $seat_name ?? 'Your Seat';
$seatId = $seat_id ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merish Cafe - Menu Selection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        body { background: #f8f6f3; }
        .qr-header {
            background: linear-gradient(135deg, #8B7355 0%, #6B5B4F 100%);
            color: white;
            padding: 2rem;
            border-radius: 0 0 2rem 2rem;
            margin-bottom: 2rem;
        }
        .seat-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 0.5rem 1.5rem;
            border-radius: 2rem;
            font-weight: 600;
        }
        .menu-section {
            margin-bottom: 3rem;
        }
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 1.5rem;
            border-bottom: 3px solid #D4A574;
            padding-bottom: 1rem;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        .menu-card {
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
        }
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }
        .menu-image {
            width: 100%;
            height: 220px;
            background: linear-gradient(135deg, #D4A574 0%, #C39368 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }
        .menu-card-body {
            padding: 1rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .menu-card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
        }
        .menu-category {
            background: #E8D5C4;
            color: #6B5B4F;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .menu-price {
            color: #D4A574;
            font-weight: 700;
            font-size: 1rem;
        }
        .menu-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .menu-description {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 1rem;
            flex: 1;
        }
        .qty-selector {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }
        .qty-input {
            width: 50px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            padding: 0.25rem;
        }
        .add-to-cart-btn {
            background: #8B7355;
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }
        .add-to-cart-btn:hover {
            background: #6B5B4F;
        }
        .cart-summary {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 2px solid #D4A574;
            padding: 1rem;
            box-shadow: 0 -4px 12px rgba(0,0,0,0.1);
            z-index: 100;
        }
        .cart-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }
        .checkout-btn {
            width: 100%;
            background: #8B7355;
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        .checkout-btn:hover {
            background: #6B5B4F;
        }
        .checkout-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .container-with-cart {
            margin-bottom: 150px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="qr-header">
        <div class="container">
            <h1 class="mb-2">☕ Merish Cafe</h1>
            <p class="mb-0">
                <span class="seat-badge">
                    <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($seatName); ?>
                </span>
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container container-with-cart">
        <!-- Menu Sections -->
        <div class="menu-section">
            <h2 class="section-title">Pilih Menu</h2>
            
            <div class="menu-grid" id="menuGrid">
                <?php if (!empty($menus)): ?>
                    <?php foreach ($menus as $menu): ?>
                        <div class="menu-card">
                            <div class="menu-image" style="background-image: url('<?php echo htmlspecialchars($menu['image'] ?? ''); ?>'); background-size: contain; background-repeat: no-repeat; background-position: center; background-color: #ffffff; font-size: <?php echo !empty($menu['image']) ? '0' : '2rem'; ?>;">
                                <?php 
                                    if (empty($menu['image'])) {
                                        $dbCat = trim($menu['category'] ?? '');
                                        $menuCat = match (strtolower($dbCat)) {
                                            'kopi' => 'Kopi',
                                            'teh' => 'Teh',
                                            'pastry' => 'Makanan',
                                            'non coffee', 'non-coffee' => 'Smoothie',
                                            'snack' => 'Snack',
                                            default => 'Kopi',
                                        };
                                        $emoji = [
                                            'Kopi' => '☕',
                                            'Teh' => '🫖',
                                            'Smoothie' => '🥤',
                                            'Makanan' => '🍰',
                                            'Snack' => '🍪',
                                            'Dessert' => '🧁'
                                        ];
                                        echo $emoji[$menuCat] ?? '🍽️';
                                    }
                                ?>
                            </div>
                            <div class="menu-card-body">
                                <div class="menu-card-meta">
                                    <span class="menu-category"><?php echo htmlspecialchars($menu['category'] ?? 'Kopi'); ?></span>
                                    <span class="menu-price">IDR <?php echo number_format($menu['price']); ?></span>
                                </div>
                                <h3 class="menu-name"><?php echo htmlspecialchars($menu['menu_name']); ?></h3>
                                <p class="menu-description"><?php echo htmlspecialchars(!empty($menu['description']) ? $menu['description'] : $menu['menu_name']); ?></p>
                                
                                <form class="add-to-cart-form" method="POST" action="index.php?page=qrorder&action=addItem">
                                    <div class="qty-selector">
                                        <label>Qty:</label>
                                        <input type="number" name="qty" class="qty-input" value="1" min="1" max="10">
                                    </div>
                                    <input type="hidden" name="menu_id" value="<?php echo $menu['menu_id']; ?>">
                                    <button type="submit" class="add-to-cart-btn">Tambah ke Keranjang</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">Menu tidak tersedia</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Cart Summary (Fixed Bottom) -->
    <div class="cart-summary">
        <div class="container">
            <div class="cart-info">
                <span>
                    <i class="bi bi-cart3"></i>
                    <span id="cartCount">0</span> item(s)
                </span>
                <span id="cartTotal">IDR 0</span>
            </div>
            <button id="checkoutBtn" class="checkout-btn" disabled>
                Lanjut ke Pembayaran
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle add to cart
        document.querySelectorAll('.add-to-cart-form').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const formData = new FormData(form);
                try {
                    const response = await fetch('index.php?page=qrorder&action=addItem', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();
                    
                    if (data.status === 'success') {
                        updateCartDisplay();
                        // Show success message
                        const btn = form.querySelector('.add-to-cart-btn');
                        const originalText = btn.innerText;
                        btn.innerText = '✓ Ditambahkan';
                        setTimeout(() => {
                            btn.innerText = originalText;
                        }, 1500);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Gagal menambahkan item');
                }
            });
        });

        // Update cart display
        async function updateCartDisplay() {
            try {
                const response = await fetch('index.php?page=qrorder&action=getCart');
                const data = await response.json();
                
                if (data.status === 'success') {
                    document.getElementById('cartCount').innerText = data.data.count;
                    document.getElementById('cartTotal').innerText = 'IDR ' + 
                        data.data.total.toLocaleString('id-ID');
                    document.getElementById('checkoutBtn').disabled = data.data.count === 0;
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Initialize cart display
        updateCartDisplay();

        // Handle checkout button
        document.getElementById('checkoutBtn').addEventListener('click', () => {
            window.location.href = 'index.php?page=qrorder&action=checkout';
        });

        // Refresh cart every 2 seconds
        setInterval(updateCartDisplay, 2000);
    </script>
</body>
</html>
