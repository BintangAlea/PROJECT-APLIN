<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe Cashier - Merish System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f7eeed;
            --panel: #fff8f6;
            --line: #eadbd7;
            --ink: #342629;
            --muted: #8b7074;
            --accent: #8f6771;
            --accent-soft: #f5dde2;
        }
        body {
            background: radial-gradient(circle at top left, #232323 0, #141414 24%, #0f0f0f 100%);
            font-family: 'Inter', sans-serif;
            color: var(--ink);
        }
        .shell { min-height: 100vh; padding: 10px; }
        .app-frame {
            min-height: calc(100vh - 20px);
            border-radius: 14px;
            overflow: hidden;
            background: var(--bg);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
        }
        .sidebar {
            background: rgba(250, 241, 240, 0.92);
            border-right: 1px solid var(--line);
            min-height: calc(100vh - 20px);
        }
        .brand-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            line-height: 0.95;
            color: #6f4e56;
        }
        .brand-subtitle { color: var(--muted); font-size: 0.85rem; }
        .avatar {
            width: 62px; height: 62px; border-radius: 50%; object-fit: cover;
            border: 2px solid #f1dedb; background: #f1dedb;
        }
        .sidebar-link {
            display: flex; align-items: center; gap: 0.65rem;
            padding: 0.85rem 1rem; border-radius: 10px; color: #6f5a5d;
            text-decoration: none; margin-bottom: 0.35rem;
            transition: all 0.2s ease; border: 1px solid transparent;
        }
        .sidebar-link:hover { background: rgba(244, 219, 227, 0.55); color: var(--ink); }
        .sidebar-link.active {
            background: linear-gradient(90deg, rgba(244, 219, 227, 0.95), rgba(239, 209, 221, 0.9));
            border-color: rgba(143, 103, 113, 0.15); color: var(--ink);
        }
        .sidebar-footer { border-top: 1px solid var(--line); }
        
        .main-topbar {
            background: rgba(255, 248, 246, 0.8);
            border-bottom: 1px solid var(--line);
            backdrop-filter: blur(10px);
        }
        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2rem, 3vw, 3rem);
            color: #7d5a62;
            margin-bottom: 0;
        }
        .page-subtitle { color: var(--muted); margin-bottom: 0; }
        
        .panel {
            background: rgba(255, 250, 248, 0.76); border: 1px solid var(--line);
            border-radius: 10px; box-shadow: 0 8px 24px rgba(118, 83, 89, 0.06);
        }
        
        .tab-btn {
            padding: 0.5rem 1.2rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.2s;
        }
        .tab-btn.active {
            background: var(--accent);
            color: #fff;
        }
        .tab-btn.inactive {
            background: #fff;
            border-color: var(--line);
            color: var(--muted);
        }
        .tab-btn:hover {
            opacity: 0.9;
        }

        .order-row-link {
            display: block;
            text-decoration: none;
            color: inherit;
            padding: 0.85rem 1rem;
            border-radius: 8px;
            border: 1px solid var(--line);
            background: #fff;
            margin-bottom: 0.65rem;
            transition: all 0.2s;
        }
        .order-row-link:hover {
            border-color: var(--accent);
            box-shadow: 0 4px 12px rgba(143, 103, 113, 0.08);
        }
        .order-row-link.active {
            border-color: var(--accent);
            background: var(--accent-soft);
        }
        
        .invoice-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 1.5rem;
        }
        .invoice-header {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: #79555c;
            border-bottom: 1px dashed var(--line);
            padding-bottom: 0.75rem;
            margin-bottom: 1rem;
        }
        .invoice-meta {
            font-size: 0.85rem;
            color: var(--muted);
            margin-bottom: 1rem;
        }
        .invoice-item {
            display: flex;
            justify-content: space-between;
            font-size: 0.92rem;
            margin-bottom: 0.5rem;
        }
        .invoice-total {
            border-top: 1px dashed var(--line);
            padding-top: 0.75rem;
            margin-top: 1rem;
            font-weight: 700;
            font-size: 1.1rem;
            display: flex;
            justify-content: space-between;
        }
        
        .form-select, .form-control {
            border-radius: 6px;
            border: 1px solid var(--line);
        }
        .btn-submit {
            background: var(--accent);
            border: 0;
            color: #fff;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.8rem;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            transition: background-color 0.2s;
        }
        .btn-submit:hover {
            background: #7a5862;
        }
    </style>
</head>
<body>
<div class="shell">
    <div class="app-frame row g-0">
        <aside class="col-lg-2 sidebar d-flex flex-column">
            <div class="p-4">
                <div class="d-flex justify-content-center mb-3">
                    <img class="avatar" src="assets/merish_pictures/barista/Dimas.jpg" alt="Barista profile">
                </div>
                <div class="text-center mb-4">
                    <div class="brand-title">Merish<br>Barista</div>
                    <div class="brand-subtitle mt-2">Online</div>
                </div>
                <a class="sidebar-link" href="index.php?page=barista">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="2" x2="6" y2="4"></line><line x1="10" y1="2" x2="10" y2="4"></line><line x1="14" y1="2" x2="14" y2="4"></line></svg>
                    <span>KDS Board</span>
                </a>
                <a class="sidebar-link" href="index.php?page=barista&action=menuAvailability">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                    <span>Menu Availability</span>
                </a>
                <a class="sidebar-link active" href="index.php?page=barista&action=paymentCashier">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                    <span>Cafe Cashier</span>
                </a>
            </div>
            <div class="mt-auto p-4 sidebar-footer">
                <a class="sidebar-link text-danger" href="<?= LOGOUT_URL ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <section class="col-lg-10 d-flex flex-column">
            <div class="main-topbar px-4 px-lg-5 py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="page-title">Cafe Cashier</h1>
                    <p class="page-subtitle">Check payment status and settle cafe transactions.</p>
                </div>
            </div>

            <div class="p-4 p-lg-5 flex-grow-1">
                <?php if (!empty($flashSuccess)): ?>
                    <div class="alert alert-success border-0 rounded-0 mb-4 small"><?php echo htmlspecialchars($flashSuccess); ?></div>
                <?php endif; ?>
                <?php if (!empty($flashError)): ?>
                    <div class="alert alert-danger border-0 rounded-0 mb-4 small"><?php echo htmlspecialchars($flashError); ?></div>
                <?php endif; ?>

                <div class="mb-4 d-flex gap-2">
                    <a href="index.php?page=barista&action=paymentCashier&filter=unpaid" class="tab-btn <?php echo $statusFilter === 'unpaid' ? 'active' : 'inactive'; ?>">Belum Bayar (Unpaid)</a>
                    <a href="index.php?page=barista&action=paymentCashier&filter=paid" class="tab-btn <?php echo $statusFilter === 'paid' ? 'active' : 'inactive'; ?>">Sudah Bayar (Paid)</a>
                </div>

                <div class="row g-4">
                    <!-- Left: Orders List -->
                    <div class="col-md-6">
                        <div class="panel p-4">
                            <h3 class="h5 mb-3" style="color: #6f4e56; font-weight:600;">Daftar Pesanan</h3>
                            
                            <?php if (empty($orders)): ?>
                                <div class="text-muted small py-3">Tidak ada pesanan dengan status ini.</div>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                    <?php 
                                    $isActive = ($selectedOrderId === (int)$order['order_id']); 
                                    $seatLabel = !empty($order['seat_id']) ? $order['seat_id'] : 'Takeaway';
                                    ?>
                                    <a href="index.php?page=barista&action=paymentCashier&filter=<?php echo $statusFilter; ?>&order_id=<?php echo $order['order_id']; ?>" class="order-row-link <?php echo $isActive ? 'active' : ''; ?>">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong style="color: #4a3539;">#ORD-<?php echo str_pad((string)$order['order_id'], 3, '0', STR_PAD_LEFT); ?></strong>
                                            <span class="badge <?php echo $order['payment_status'] === 'Paid' ? 'bg-success' : 'bg-warning text-dark'; ?> fs-7">
                                                <?php echo $order['payment_status']; ?>
                                            </span>
                                        </div>
                                        <div class="small text-muted mb-2">Pelanggan: <?php echo htmlspecialchars($order['guest_name']); ?> (<?php echo htmlspecialchars($seatLabel); ?>)</div>
                                        <div class="small text-truncate text-muted mb-1" style="max-width:380px;"><?php echo htmlspecialchars($order['items_summary'] ?? ''); ?></div>
                                        <div class="d-flex justify-content-between align-items-center mt-2 border-top pt-2">
                                            <span class="small text-muted"><?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></span>
                                            <strong class="text-accent" style="color:#8f6771;">Rp <?php echo number_format((float)$order['total_amount'], 0, ',', '.'); ?></strong>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Right: Selected Order Details & Payment Settlement -->
                    <div class="col-md-6">
                        <?php if (!$selectedOrder): ?>
                            <div class="panel p-4 text-center text-muted">
                                Silakan pilih pesanan di sebelah kiri untuk memproses pembayaran.
                            </div>
                        <?php else: ?>
                            <div class="invoice-card">
                                <h3 class="invoice-header">Struk Tagihan</h3>
                                
                                <div class="invoice-meta d-flex justify-content-between mb-3">
                                    <div>
                                        <div><strong>No. Order:</strong> #ORD-<?php echo str_pad((string)$selectedOrder['order_id'], 3, '0', STR_PAD_LEFT); ?></div>
                                        <div><strong>Pelanggan:</strong> <?php echo htmlspecialchars($selectedOrder['guest_name']); ?></div>
                                        <div><strong>Kursi/Lokasi:</strong> <?php echo htmlspecialchars(!empty($selectedOrder['seat_id']) ? $selectedOrder['seat_id'] : 'Takeaway'); ?></div>
                                    </div>
                                    <div class="text-end">
                                        <div><strong>Tanggal:</strong></div>
                                        <div><?php echo date('d M Y', strtotime($selectedOrder['order_date'])); ?></div>
                                        <div><?php echo date('h:i A', strtotime($selectedOrder['order_date'])); ?></div>
                                    </div>
                                </div>

                                <div class="invoice-items py-2">
                                    <?php 
                                    $subtotal = 0;
                                    foreach ($selectedOrderDetails as $item): 
                                        $subtotal += (float)$item['subtotal'];
                                    ?>
                                        <div class="invoice-item">
                                            <span><?php echo htmlspecialchars($item['menu_name']); ?> (x<?php echo (int)$item['qty']; ?>)</span>
                                            <span>Rp <?php echo number_format((float)$item['subtotal'], 0, ',', '.'); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <?php
                                // Sync tax with 10% cafe tax
                                $tax = $subtotal * 0.1;
                                $calculatedTotal = $subtotal + $tax;
                                ?>
                                <div class="invoice-item text-muted mt-2 border-top pt-2">
                                    <span>Subtotal</span>
                                    <span>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></span>
                                </div>
                                <div class="invoice-item text-muted">
                                    <span>Tax & Service (10%)</span>
                                    <span>Rp <?php echo number_format($tax, 0, ',', '.'); ?></span>
                                </div>

                                <div class="invoice-total">
                                    <span>Total Bayar</span>
                                    <span>Rp <?php echo number_format((float)$selectedOrder['total_amount'], 0, ',', '.'); ?></span>
                                </div>

                                <?php if ($selectedOrder['payment_status'] === 'Unpaid'): ?>
                                    <form method="POST" action="index.php?page=barista&action=settlePayment" class="mt-4 border-top pt-3">
                                        <input type="hidden" name="order_id" value="<?php echo $selectedOrder['order_id']; ?>">
                                        
                                        <div class="mb-3">
                                            <label class="form-label small text-uppercase fw-semibold" style="color:var(--muted)">Metode Pembayaran</label>
                                            <select class="form-select" name="payment_method" required>
                                                <option value="Cash" <?php echo $selectedOrder['payment_method'] === 'Cash' ? 'selected' : ''; ?>>Cash (Tunai)</option>
                                                <option value="QRIS" <?php echo $selectedOrder['payment_method'] === 'QRIS' ? 'selected' : ''; ?>>QRIS (Auto-Paid)</option>
                                            </select>
                                        </div>

                                        <button type="submit" class="btn-submit w-100">Selesaikan Pembayaran</button>
                                    </form>
                                <?php else: ?>
                                    <div class="mt-4 p-3 bg-success bg-opacity-10 text-success text-center rounded-2 border border-success border-opacity-25 small fw-semibold">
                                        LUNAS (Paid via <?php echo htmlspecialchars($selectedOrder['payment_method']); ?>)
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
</body>
</html>
