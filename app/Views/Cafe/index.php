<?php 
$pageTitle = "The Cafe - MERISH";
include __DIR__ . '/../Layout/header.php'; 
?>

<!-- Cafe Hero -->
<section style="background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%); color: white; padding: 60px 0;">
    <div class="container">
        <h1 class="mb-3">☕ The Cafe - MERISH</h1>
        <p class="lead mb-0">Nikmati minuman premium sambil bersantai atau menunggu treatment di salon</p>
    </div>
</section>

<!-- QR Code Scanner Section (untuk Dine-In) -->
<?php if (!isset($_SESSION['seat_id']) && !isset($_GET['seat_token'])): ?>
<section class="py-4 bg-warning bg-opacity-10 border-top border-bottom">
    <div class="container">
        <div class="alert alert-info" role="alert">
            <strong><i class="fas fa-qrcode"></i> Scan QR Code:</strong> 
            Silakan scan QR code di meja atau kursi salon Anda untuk memesan (Dine-In). 
            Atau lanjutkan sebagai <strong>Takeaway</strong>.
        </div>
    </div>
</section>
<?php elseif (isset($_SESSION['seat_id'])): ?>
<section class="py-4" style="background: linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%);">
    <div class="container">
        <div class="alert alert-success" role="alert">
            <i class="fas fa-check-circle"></i> <strong>Seat Detected:</strong> 
            Anda berada di kursi <code><?php echo htmlspecialchars($_SESSION['seat_id']); ?></code>
            - Pesanan akan diantar ke tempat Anda!
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Menu Catalog -->
<section class="py-5">
    <div class="container">
        <h2 class="section-title">☕ Pilihan Menu</h2>
        
        <?php if (count($menus) > 0): ?>
            <div class="row g-4">
                <?php foreach ($menus as $menu): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-service h-100">
                            <div style="background: linear-gradient(135deg, #D2B48C 0%, #DEB887 100%); height: 150px; display: flex; align-items: center; justify-content: center; font-size: 3rem;">
                                🍵
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($menu['menu_name']); ?></h5>
                                <p class="card-text text-muted small">Premium quality, fresh ingredients</p>
                                <h6 class="text-merish" style="color: var(--merish-primary);">
                                    <strong>Rp <?php echo number_format($menu['price'], 0, ',', '.'); ?></strong>
                                </h6>
                                <?php if ($menu['is_available']): ?>
                                    <form method="POST" action="index.php?page=cafe&action=addToCart" class="mt-3">
                                        <input type="hidden" name="menu_id" value="<?php echo htmlspecialchars($menu['menu_id']); ?>">
                                        <div class="input-group mb-2">
                                            <button class="btn btn-outline-secondary" type="button" onclick="decreaseQty(this)">−</button>
                                            <input type="number" name="qty" class="form-control text-center" value="1" min="1" max="10">
                                            <button class="btn btn-outline-secondary" type="button" onclick="increaseQty(this)">+</button>
                                        </div>
                                        <button type="submit" class="btn btn-merish w-100">
                                            <i class="fas fa-shopping-cart"></i> Add to Cart
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <div class="alert alert-secondary text-center mt-3 py-2">
                                        <small>Out of Stock</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-muted">Menu tidak tersedia</p>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="index.php?page=cafe&action=viewCart" class="btn btn-merish btn-lg">
                <i class="fas fa-shopping-cart"></i> View Cart (<?php echo count($_SESSION['cart'] ?? []); ?> items)
            </a>
        </div>
    </div>
</section>

<!-- Info Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-4 text-center mb-3">
                <i class="fas fa-shipping-fast fa-2x mb-3" style="color: var(--merish-primary);"></i>
                <h5>Pengiriman Cepat</h5>
                <p class="text-muted">Pesanan Anda akan diantar dalam 5-10 menit</p>
            </div>
            <div class="col-md-4 text-center mb-3">
                <i class="fas fa-percent fa-2x mb-3" style="color: var(--merish-primary);"></i>
                <h5>Diskon Bundle</h5>
                <p class="text-muted">Hemat 20% saat menggabungkan pesanan salon & cafe</p>
            </div>
            <div class="col-md-4 text-center mb-3">
                <i class="fas fa-credit-card fa-2x mb-3" style="color: var(--merish-primary);"></i>
                <h5>Berbagai Metode</h5>
                <p class="text-muted">Cash, Card, QRIS, atau tambah ke tagihan salon</p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../Layout/footer.php'; ?>

<script>
function increaseQty(btn) {
    const input = btn.parentElement.querySelector('input[name="qty"]');
    input.value = Math.min(10, parseInt(input.value) + 1);
}

function decreaseQty(btn) {
    const input = btn.parentElement.querySelector('input[name="qty"]');
    input.value = Math.max(1, parseInt(input.value) - 1);
}
</script>
