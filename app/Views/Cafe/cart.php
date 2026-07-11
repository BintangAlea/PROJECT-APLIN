<?php
$pageTitle = 'Cart - MERISH Cafe';
$isLoggedIn = isset($_SESSION['user_id']);
$displayName = $_SESSION['full_name'] ?? $_SESSION['user_login'] ?? 'Guest';
$cart = $_SESSION['cart'] ?? [];
$menus = $menus ?? [];

$cartItems = [];
$totalPrice = 0;
foreach ($cart as $item) {
    $qty = (int) ($item['qty'] ?? 0);
    $price = (int) ($item['price'] ?? 0);
    $lineTotal = $price * $qty;
    $totalPrice += $lineTotal;
    $cartItems[] = [
        'menu_id' => $item['menu_id'] ?? '',
        'name' => $item['menu_name'] ?? 'Menu Item',
        'qty' => $qty,
        'price' => $price,
        'line_total' => $lineTotal,
        'category' => $item['category'] ?? 'kopi',
        'image' => $item['image'] ?? '',
    ];
}

$tax = (int) round($totalPrice * 0.1);
$finalTotal = $totalPrice + $tax;
$orderType = $_SESSION['cafe_order']['order_type'] ?? 'Dine-In';
$selectedGuest = $_SESSION['cafe_order']['guest_name'] ?? $displayName;
$seatId = $_GET['seat'] ?? $_GET['seat_id'] ?? $_GET['table'] ?? $_SESSION['qr_order']['seat_id'] ?? $_SESSION['seat_id'] ?? null;

// If user is a logged-in customer and no seat is explicitly scanned/passed,
// automatically find their active salon reservation seat for today
if (!$seatId && $isLoggedIn) {
    try {
        $db = \App\Core\Database::getConnection();
        $stmt = $db->prepare("
            SELECT r.seat_id 
            FROM reservations r
            WHERE r.user_id = :user_id 
              AND r.STATUS IN ('Confirmed', 'In-Service')
            ORDER BY r.schedule_time DESC
            LIMIT 1
        ");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
        $row = $stmt->fetch();
        if ($row && !empty($row['seat_id'])) {
            $seatId = $row['seat_id'];
        }
    } catch (\Exception $e) {
        // Safe fallback
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #fcf4f2;
            --panel: #fffdfc;
            --line: #eadfdd;
            --ink: #514248;
            --muted: #7d6f73;
            --accent: #8c6674;
            --accent-dark: #764f5c;
            --soft: #f5ecea;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: 'Montserrat', sans-serif;
        }

        .page-shell {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            padding: 1.05rem 0 0.4rem;
        }

        .brand {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.2rem, 4vw, 3.8rem);
            color: var(--accent);
            text-decoration: none;
            line-height: 1;
        }

        .back-link {
            color: #8d7c80;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .hero {
            margin-top: 0.3rem;
            margin-bottom: 2rem;
        }

        .title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.5rem, 4vw, 4.1rem);
            line-height: 1;
            color: var(--accent);
            margin: 0 0 0.45rem;
        }

        .chips {
            display: flex;
            gap: 0.45rem;
            flex-wrap: wrap;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            background: #efe0ef;
            border: 1px solid #e1cce5;
            color: #7f5e73;
            border-radius: 2px;
            padding: 0.18rem 0.5rem;
            font-size: 0.64rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.1px;
            line-height: 1;
        }

        .chip.secondary {
            background: #f0eceb;
            border-color: #ddd2d1;
            color: #6d6666;
        }

        .layout {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(290px, 0.82fr);
            gap: 2.8rem;
            align-items: start;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            color: #3f3137;
            font-size: 1.5rem;
            margin-bottom: 1.3rem;
        }

        .menu-picker {
            margin-bottom: 2rem;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.9rem;
        }

        .menu-card {
            border: 1px solid #e6dada;
            background: #fffafa;
            box-shadow: 0 10px 20px rgba(78, 54, 61, 0.04);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }

        .menu-card-visual {
            height: 260px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            background-color: #ffffff;
            border-bottom: 1px solid #eadede;
        }

        .menu-card-body {
            padding: 0.9rem 0.85rem 0.95rem;
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
            flex: 1;
        }

        .menu-card-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem;
            color: #3d3036;
            line-height: 1.1;
            margin: 0;
        }

        .menu-card-meta {
            display: flex;
            justify-content: space-between;
            gap: 0.75rem;
            align-items: center;
            color: #7a6d71;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .menu-card-price {
            color: var(--accent);
            font-weight: 700;
            font-size: 0.88rem;
        }

        .menu-card-desc {
            color: #75696d;
            font-size: 0.78rem;
            line-height: 1.5;
            margin: 0;
        }

        .add-menu-btn {
            margin-top: auto;
            border: 1px solid #b58f9b;
            background: #fff;
            color: #7d5161;
            text-transform: uppercase;
            letter-spacing: 1.15px;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.62rem 0.8rem;
            width: 100%;
        }

        .add-menu-btn:hover {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }

        .cart-item {
            display: grid;
            grid-template-columns: 70px minmax(0, 1fr) 110px;
            gap: 1rem;
            align-items: center;
            padding: 0.7rem 0 1rem;
            border-bottom: 1px solid var(--line);
        }

        .thumb {
            width: 70px;
            height: 70px;
            border-radius: 2px;
            border: 1px solid #e6dcdc;
            background-size: cover;
            background-position: center;
            overflow: hidden;
        }

        .thumb.signature-latte {
            background-image: radial-gradient(circle at 50% 48%, #e9dccd 0 22%, transparent 23%), linear-gradient(135deg, #e9e0c9 0%, #b9a790 40%, #7d6b58 100%);
        }

        .thumb.butter-croissant {
            background-image: radial-gradient(circle at 55% 65%, #b76d32 0 24%, transparent 25%), linear-gradient(135deg, #f3dcc3 0%, #b8936e 54%, #7b5d45 100%);
        }

        .thumb.rose-latte {
            background-image: radial-gradient(circle at 50% 45%, #e1d2cf 0 20%, transparent 21%), linear-gradient(135deg, #d8c0bc 0%, #a67b75 45%, #83635f 100%);
        }

        .thumb.croissant {
            background-image: radial-gradient(circle at 46% 70%, #b57b44 0 22%, transparent 23%), linear-gradient(135deg, #ccb69d 0%, #b9946e 58%, #83624c 100%);
        }

        .item-name {
            font-size: 1rem;
            font-weight: 500;
            color: #251c21;
            margin-bottom: 0.2rem;
        }

        .item-sub {
            color: var(--muted);
            font-size: 0.78rem;
            margin-bottom: 0.3rem;
        }

        .item-price {
            color: var(--accent);
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }

        .qty-box {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.8rem;
            background: #f6eded;
            border: 1px solid #e1d4d5;
            border-radius: 10px;
            padding: 0.42rem 0.55rem;
            min-width: 92px;
        }

        .qty-box button {
            border: 0;
            background: transparent;
            color: #6e5b60;
            font-size: 1.1rem;
            line-height: 1;
            font-weight: 500;
        }

        .qty-box .value {
            width: 18px;
            text-align: center;
            color: #54454a;
            font-weight: 600;
        }

        .delivery-title {
            font-family: 'Playfair Display', serif;
            color: #3f3137;
            font-size: 1.5rem;
            margin: 2rem 0 1rem;
        }

        .delivery-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.7rem;
        }

        .delivery-card {
            border: 1px solid #e2d6d7;
            background: #faf3f3;
            padding: 1rem 0.9rem 0.95rem;
            min-height: 124px;
            text-align: center;
            cursor: pointer;
            transition: 0.2s ease;
            position: relative;
        }

        .delivery-card.active {
            background: #f4eceb;
            border-color: #b38e99;
            box-shadow: inset 0 0 0 1px rgba(179, 142, 153, 0.14);
        }

        .delivery-icon {
            width: 28px;
            height: 28px;
            margin: 0 auto 0.9rem;
            color: #6d5b60;
            font-size: 1.15rem;
        }

        .delivery-icon.check {
            position: absolute;
            top: 11px;
            right: 12px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #8c6674;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            opacity: 0;
        }

        .delivery-card.active .delivery-icon.check {
            opacity: 1;
        }

        .delivery-name {
            font-size: 0.92rem;
            color: #2f2429;
            margin-bottom: 0.18rem;
        }

        .delivery-sub {
            color: var(--muted);
            font-size: 0.8rem;
        }

        .summary-card {
            background: var(--panel);
            border: 1px solid #eee1df;
            box-shadow: 0 12px 26px rgba(70, 48, 55, 0.05);
            padding: 1.6rem 1.45rem 1.4rem;
            position: sticky;
            top: 1.25rem;
        }

        .summary-top {
            text-align: center;
            color: #8d7a80;
            font-family: 'Playfair Display', serif;
            font-size: 1.35rem;
            margin-bottom: 0.1rem;
        }

        .summary-title {
            text-align: center;
            font-family: 'Playfair Display', serif;
            font-size: 1.7rem;
            color: #3f3137;
            margin-bottom: 1.2rem;
        }

        .summary-line {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            font-size: 0.85rem;
            color: #6d5d63;
            margin-bottom: 0.8rem;
        }

        .summary-divider {
            border-top: 1px dashed #e8dddd;
            margin: 1rem 0;
        }

        .summary-footer {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 1rem;
            margin-top: 0.6rem;
        }

        .summary-footer .label {
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 0.72rem;
            font-weight: 700;
            color: #423339;
        }

        .summary-footer .value {
            font-family: 'Playfair Display', serif;
            color: var(--accent);
            font-size: clamp(2rem, 4vw, 3rem);
            line-height: 1;
        }

        .checkout-btn {
            width: 100%;
            margin-top: 1.1rem;
            border: 0;
            background: var(--accent);
            color: #fff;
            padding: 0.95rem 1rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-size: 0.76rem;
            font-weight: 700;
            box-shadow: 0 10px 18px rgba(140, 102, 116, 0.14);
        }

        .checkout-btn:hover {
            background: var(--accent-dark);
            color: #fff;
        }

        .footer-spacer {
            margin-top: 4rem;
            height: 1px;
            background: #eadfdf;
        }

        .footer {
            background: #f1e9e8;
            border-top: 1px solid #e2d7d7;
            padding: 2rem 0;
            margin-top: auto;
        }

        .footer-brand {
            font-family: 'Playfair Display', serif;
            font-size: 2.8rem;
            color: var(--accent);
            text-decoration: none;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 1.2rem;
            flex-wrap: wrap;
        }

        .footer-links a,
        .footer-copy {
            color: #74686d;
            font-size: 0.84rem;
            text-decoration: none;
        }

        .empty-state {
            border: 1px dashed #dfcecf;
            background: rgba(255,255,255,0.35);
            padding: 1.2rem;
            color: #7b6f73;
        }

        @media (max-width: 991.98px) {
            .layout {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .menu-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .summary-card {
                position: static;
            }
        }

        @media (max-width: 575.98px) {
            .menu-grid {
                grid-template-columns: 1fr;
            }

            .cart-item {
                grid-template-columns: 62px minmax(0, 1fr);
            }

            .qty-box {
                grid-column: 2 / -1;
                justify-self: start;
                margin-top: 0.4rem;
            }

            .delivery-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="page-shell">
    <div class="container topbar">
        <div class="d-flex justify-content-between align-items-start">
            <a class="brand" href="index.php?page=home">Merish</a>
            <a class="back-link" href="index.php?page=home">← Back</a>
        </div>
    </div>

    <main class="container py-3 py-md-4">
        <div class="hero">
            <h1 class="title">Halo, Kak <?php echo htmlspecialchars($displayName); ?>!</h1>

        </div>

        <div class="layout">
            <section>
                <div class="menu-picker">
                    <h2 class="section-title">Pilih Menu</h2>
                    <div class="menu-grid">
                        <?php foreach ($menus as $menu): ?>
                            <?php
                            $menuId = (string) ($menu['menu_id'] ?? '');
                            $menuName = (string) ($menu['menu_name'] ?? 'Menu Item');
                            $menuPrice = (int) ($menu['price'] ?? 0);
                            $dbCat = trim($menu['category'] ?? '');
                            if ($dbCat === '') {
                                $nameLower = strtolower($menuName);
                                if (str_contains($nameLower, 'kopi') || str_contains($nameLower, 'coffee') || str_contains($nameLower, 'latte') || str_contains($nameLower, 'espresso')) {
                                    $dbCat = 'Kopi';
                                } elseif (str_contains($nameLower, 'teh') || str_contains($nameLower, 'tea') || str_contains($nameLower, 'matcha')) {
                                    $dbCat = 'Teh';
                                } elseif (str_contains($nameLower, 'croissant') || str_contains($nameLower, 'pastry') || str_contains($nameLower, 'cake') || str_contains($nameLower, 'roti')) {
                                    $dbCat = 'Pastry';
                                } else {
                                    $dbCat = 'Kopi';
                                }
                            }
                            $menuCategory = match (strtolower($dbCat)) {
                                'kopi' => 'kopi',
                                'teh' => 'teh',
                                'pastry' => 'pastry',
                                'non coffee', 'non-coffee' => 'non-coffee',
                                'snack' => 'pastry',
                                default => 'kopi',
                            };
                            $desc = !empty($menu['description']) ? $menu['description'] : 'Tambahkan menu ini ke keranjang sebelum lanjut ke pembayaran.';
                            $image = trim($menu['image'] ?? '');
                            if (str_contains($image, 'almond croissant.jpg')) {
                                $image = str_replace('almond croissant.jpg', 'almond croissant.png', $image);
                            } elseif (str_contains($image, 'cookies cream.jpg')) {
                                $image = str_replace('cookies cream.jpg', 'cookies cream.png', $image);
                            } elseif (str_contains($image, 'espresso.jpg')) {
                                $image = str_replace('espresso.jpg', 'espresso.png', $image);
                            } elseif (str_contains($image, 'tiramisu cake.webp')) {
                                $image = str_replace('tiramisu cake.webp', 'tiramisu cake.jpg', $image);
                            } elseif (str_contains($image, 'Truffle fries.webp')) {
                                $image = str_replace('Truffle fries.webp', 'truffle fries.png', $image);
                            }

                            if ($image === '') {
                                $image = match ($menuCategory) {
                                    'teh' => 'assets/MERISH_PICTURES/CAFE/jasmine tea.jpg',
                                    'pastry' => 'assets/MERISH_PICTURES/CAFE/almond croissant.png',
                                    'non-coffee' => 'assets/MERISH_PICTURES/CAFE/choco frappe.jpg',
                                    default => 'assets/MERISH_PICTURES/CAFE/americano.jpg',
                                };
                            }
                            ?>
                            <article class="menu-card">
                                <div class="menu-card-visual" style="background-image: url('<?php echo htmlspecialchars($image); ?>'); background-size: contain; background-repeat: no-repeat; background-position: center; background-color: #ffffff;"></div>
                                <div class="menu-card-body">
                                    <div class="menu-card-meta">
                                        <span><?php echo htmlspecialchars($menuCategory); ?></span>
                                        <span class="menu-card-price">IDR <?php echo number_format($menuPrice, 0, ',', '.'); ?></span>
                                    </div>
                                    <h3 class="menu-card-name"><?php echo htmlspecialchars($menuName); ?></h3>
                                    <p class="menu-card-desc"><?php echo htmlspecialchars($desc); ?></p>
                                    <form method="POST" action="index.php?page=cafe&action=addToCart" class="m-0">
                                        <input type="hidden" name="menu_id" value="<?php echo htmlspecialchars($menuId); ?>">
                                        <input type="hidden" name="menu_name" value="<?php echo htmlspecialchars($menuName); ?>">
                                        <input type="hidden" name="price" value="<?php echo htmlspecialchars((string) $menuPrice); ?>">
                                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($menuCategory); ?>">
                                        <input type="hidden" name="image" value="<?php echo htmlspecialchars($image); ?>">
                                        <?php if ($seatId): ?>
                                            <input type="hidden" name="seat" value="<?php echo htmlspecialchars($seatId); ?>">
                                        <?php endif; ?>
                                        <button type="submit" class="add-menu-btn">Tambah ke Keranjang</button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>

                <h2 class="section-title">Your Order</h2>
                 <?php if (!empty($cartItems)): ?>
                    <?php foreach ($cartItems as $item): ?>
                        <?php
                        $itemCat = trim($item['category'] ?? 'kopi');
                        $image = !empty($item['image']) ? $item['image'] : 'assets/MERISH_PICTURES/CAFE/americano.jpg';
                        ?>
                        <div class="cart-item">
                            <div class="thumb" style="background-image: url('<?php echo htmlspecialchars($image); ?>'); background-size: cover; background-position: center;"></div>
                            <div>
                                <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                <div class="item-sub"><?php echo $itemCat === 'pastry' ? 'Warmed' : 'Oat Milk, Less Sugar'; ?></div>
                                <div class="item-price">IDR <?php echo number_format($item['price'], 0, ',', '.'); ?></div>
                            </div>
                            <div class="qty-box">
                                <form method="POST" action="index.php?page=cafe&action=updateCart" class="m-0 d-inline">
                                    <input type="hidden" name="menu_id" value="<?php echo htmlspecialchars($item['menu_id']); ?>">
                                    <?php if ($seatId): ?>
                                        <input type="hidden" name="seat" value="<?php echo htmlspecialchars($seatId); ?>">
                                    <?php endif; ?>
                                    <button type="submit" name="qty" value="<?php echo max(0, $item['qty'] - 1); ?>">−</button>
                                </form>
                                <span class="value"><?php echo (int) $item['qty']; ?></span>
                                <form method="POST" action="index.php?page=cafe&action=updateCart" class="m-0 d-inline">
                                    <input type="hidden" name="menu_id" value="<?php echo htmlspecialchars($item['menu_id']); ?>">
                                    <?php if ($seatId): ?>
                                        <input type="hidden" name="seat" value="<?php echo htmlspecialchars($seatId); ?>">
                                    <?php endif; ?>
                                    <button type="submit" name="qty" value="<?php echo $item['qty'] + 1; ?>">+</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">Keranjang masih kosong. Silakan pilih menu di atas dulu sebelum checkout.</div>
                <?php endif; ?>


            </section>

            <aside>
                <div class="summary-card">
                    <div class="summary-top">Order Summary</div>
                    <div class="summary-title">Merish Cafe</div>

                    <?php if (!empty($cartItems)): ?>
                        <?php foreach ($cartItems as $item): ?>
                            <div class="summary-line align-items-center mb-3">
                                <div class="d-flex flex-column" style="max-width: 140px;">
                                    <span style="font-weight: 600; color: #3f3137; line-height: 1.2; text-align: left;"><?php echo htmlspecialchars($item['name']); ?></span>
                                    <span style="font-size: 0.72rem; color: var(--muted); text-align: left;">IDR <?php echo number_format($item['price'], 0, ',', '.'); ?></span>
                                </div>
                                <div class="d-flex align-items-center gap-2 ms-auto">
                                    <div class="qty-box" style="padding: 0.2rem 0.4rem; min-width: 76px; font-size: 0.8rem; border-radius: 6px; gap: 0.5rem; background: #faf3f3; border: 1px solid #eadede; display: inline-flex; align-items: center; justify-content: space-between;">
                                        <form method="POST" action="index.php?page=cafe&action=updateCart" class="m-0 d-inline">
                                            <input type="hidden" name="menu_id" value="<?php echo htmlspecialchars($item['menu_id']); ?>">
                                            <?php if ($seatId): ?>
                                                <input type="hidden" name="seat" value="<?php echo htmlspecialchars($seatId); ?>">
                                            <?php endif; ?>
                                            <button type="submit" name="qty" value="<?php echo max(0, $item['qty'] - 1); ?>" style="font-size: 0.85rem; padding: 0 4px; font-weight: bold; color: #8c6674; border: 0; background: transparent;">−</button>
                                        </form>
                                        <span class="value" style="font-size: 0.8rem; width: 14px; text-align: center; font-weight: 700; color: #3f3137;"><?php echo (int) $item['qty']; ?></span>
                                        <form method="POST" action="index.php?page=cafe&action=updateCart" class="m-0 d-inline">
                                            <input type="hidden" name="menu_id" value="<?php echo htmlspecialchars($item['menu_id']); ?>">
                                            <?php if ($seatId): ?>
                                                <input type="hidden" name="seat" value="<?php echo htmlspecialchars($seatId); ?>">
                                            <?php endif; ?>
                                            <button type="submit" name="qty" value="<?php echo $item['qty'] + 1; ?>" style="font-size: 0.85rem; padding: 0 4px; font-weight: bold; color: #8c6674; border: 0; background: transparent;">+</button>
                                        </form>
                                    </div>
                                    <span style="font-weight: 600; font-size: 0.82rem; min-width: 60px; text-align: right; color: #3f3137;">
                                        <?php echo number_format($item['line_total'], 0, ',', '.'); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-3 text-muted" style="font-size: 0.8rem; border: 1px dashed #eee1df; background: rgba(255,255,255,0.5); margin-bottom: 1rem;">
                            Keranjang kosong
                        </div>
                    <?php endif; ?>

                    <div class="summary-divider"></div>

                    <div class="summary-line">
                        <span>Subtotal</span>
                        <span><?php echo number_format($totalPrice, 0, ',', '.'); ?></span>
                    </div>
                    <div class="summary-line">
                        <span>Tax & Service (10%)</span>
                        <span><?php echo number_format($tax, 0, ',', '.'); ?></span>
                    </div>

                    <div class="summary-footer">
                        <div class="label">Total</div>
                        <div class="value">IDR <?php echo number_format($finalTotal, 0, ',', '.'); ?></div>
                    </div>

                    <form method="GET" action="index.php">
                        <input type="hidden" name="page" value="cafe">
                        <input type="hidden" name="action" value="checkout">
                        <?php if ($seatId): ?>
                            <input type="hidden" name="seat" value="<?php echo htmlspecialchars($seatId); ?>">
                        <?php endif; ?>
                        <input type="hidden" name="guest_name" value="<?php echo htmlspecialchars($selectedGuest); ?>">
                        <button type="submit" class="checkout-btn">Lanjut ke Pembayaran →</button>
                    </form>
                </div>
            </aside>
        </div>
    </main>

    <div class="footer-spacer"></div>
    <footer class="footer">
        <div class="container">
            <div class="row align-items-center g-3">
                <div class="col-lg-2">
                    <a class="footer-brand" href="index.php?page=home">Merish</a>
                </div>
                <div class="col-lg-5">
                    <nav class="footer-links">
                        <a href="#">Contact</a>
                        <a href="#">Location</a>
                        <a href="#">Instagram</a>
                        <a href="#">Pinterest</a>
                    </nav>
                </div>
                <div class="col-lg-5 text-lg-end">
                    <span class="footer-copy">© 2024 Merish Beauty & Cafe. All rights reserved.</span>
                </div>
            </div>
        </div>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
