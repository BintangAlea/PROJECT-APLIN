<?php 
$pageTitle = "Shopping Cart - MERISH Cafe";
include __DIR__ . '/../Layout/header.php'; 

$cart = $_SESSION['cart'] ?? [];
$totalPrice = 0;
foreach ($cart as $item) {
    $totalPrice += $item['price'] * $item['qty'];
}
?>

<!-- Cart Header -->
<section style="background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%); color: white; padding: 40px 0;">
    <div class="container">
        <h1 class="mb-0"><i class="fas fa-shopping-cart"></i> Keranjang Belanja</h1>
    </div>
</section>

<!-- Cart Items -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <?php if (count($cart) > 0): ?>
                    <div class="card">
                        <table class="table mb-0">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th>Menu</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart as $menuId => $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['menu_name']); ?></td>
                                        <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                                        <td><span class="badge bg-secondary"><?php echo $item['qty']; ?></span></td>
                                        <td><strong>Rp <?php echo number_format($item['price'] * $item['qty'], 0, ',', '.'); ?></strong></td>
                                        <td>
                                            <a href="index.php?page=cafe&action=removeFromCart&menu_id=<?php echo urlencode($menuId); ?>" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Remove
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Order Type Selection -->
                    <div class="card mt-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">📍 Metode Penerimaan</h5>
                        </div>
                        <div class="card-body">
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="order_type" id="dine_in" value="Dine-In" 
                                    <?php echo isset($_SESSION['seat_id']) ? 'checked' : 'disabled'; ?>>
                                <label class="btn btn-outline-primary" for="dine_in">
                                    <i class="fas fa-chair"></i> Antar ke Tempat Saya
                                    <?php if (!isset($_SESSION['seat_id'])): ?>
                                        <br><small>(Scan QR code terlebih dahulu)</small>
                                    <?php endif; ?>
                                </label>

                                <input type="radio" class="btn-check" name="order_type" id="takeaway" value="Takeaway" checked>
                                <label class="btn btn-outline-primary" for="takeaway">
                                    <i class="fas fa-bag-shopping"></i> Ambil Sendiri
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Checkout Form -->
                    <form method="POST" action="index.php?page=cafe&action=checkout" class="mt-4">
                        <input type="hidden" name="order_type" id="order_type_hidden" value="Takeaway">
                        
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <div class="mb-3">
                                <label class="form-label">Nama Panggilan</label>
                                <input type="text" name="guest_name" class="form-control" placeholder="Contoh: Siska" required>
                            </div>
                        <?php endif; ?>

                        <button type="submit" class="btn btn-merish btn-lg w-100" style="font-weight: 600;">
                            <i class="fas fa-credit-card"></i> Lanjut ke Pembayaran
                        </button>
                    </form>

                <?php else: ?>
                    <div class="alert alert-info text-center py-5" role="alert">
                        <h4 class="mb-3"><i class="fas fa-shopping-cart"></i></h4>
                        <p>Keranjang Anda kosong</p>
                        <a href="index.php?page=cafe" class="btn btn-merish">
                            <i class="fas fa-coffee"></i> Kembali ke Menu
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 80px;">
                    <div class="card-header" style="background: var(--merish-primary); color: white;">
                        <h5 class="mb-0">📋 Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($cart) > 0): ?>
                            <div class="mb-3">
                                <p class="mb-2">
                                    <strong>Subtotal:</strong><br>
                                    <small class="text-muted"><?php echo count($cart); ?> item(s)</small>
                                </p>
                                <p class="h5 mb-0">Rp <?php echo number_format($totalPrice, 0, ',', '.'); ?></p>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <p class="mb-2"><small class="text-muted">Estimasi Waktu Tunggu</small></p>
                                <h6 class="mb-0"><i class="fas fa-clock"></i> 5-10 Menit</h6>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <p class="mb-2"><small class="text-muted">Diskon Bundle (Jika Gabung Salon)</small></p>
                                <p class="text-success mb-0"><strong>Hemat 20%!</strong></p>
                            </div>

                            <hr>

                            <div class="bg-light p-3 rounded text-center">
                                <p class="mb-1 small"><strong>Total Bayar</strong></p>
                                <h4 style="color: var(--merish-primary);" class="mb-0">Rp <?php echo number_format($totalPrice, 0, ',', '.'); ?></h4>
                            </div>
                        <?php else: ?>
                            <p class="text-center text-muted">Keranjang kosong</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../Layout/footer.php'; ?>

<script>
// Update hidden order_type when radio changes
document.querySelectorAll('input[name="order_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('order_type_hidden').value = this.value;
    });
});
// Set initial value
const checkedRadio = document.querySelector('input[name="order_type"]:checked');
if (checkedRadio) {
    document.getElementById('order_type_hidden').value = checkedRadio.value;
}
</script>
