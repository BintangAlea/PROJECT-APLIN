<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barista KDS Board - Merish</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        * { box-sizing: border-box; }
        body {
            background: radial-gradient(circle at top left, #232323 0, #141414 24%, #0f0f0f 100%);
            font-family: 'Inter', sans-serif;
            color: var(--ink);
            margin: 0;
        }
        .shell { min-height: 100vh; padding: 10px; }
        .app-frame {
            min-height: calc(100vh - 20px);
            border-radius: 14px;
            overflow: hidden;
            background: var(--bg);
            box-shadow: 0 20px 60px rgba(0,0,0,0.35);
            display: grid;
            grid-template-columns: 200px 1fr;
        }
        .sidebar {
            background: rgba(250,241,240,0.92);
            border-right: 1px solid var(--line);
            min-height: calc(100vh - 20px);
            display: flex;
            flex-direction: column;
            padding: 1.5rem 1rem;
        }
        .brand-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem; line-height: 1; color: #6f4e56;
            text-align: center; margin-bottom: 0.25rem;
        }
        .brand-subtitle { color: var(--muted); font-size: 0.8rem; text-align: center; }
        .avatar {
            width: 56px; height: 56px; border-radius: 50%; object-fit: cover;
            border: 2px solid #f1dedb; background: #f1dedb;
            display: block; margin: 0 auto 0.75rem;
        }
        .sidebar-nav { flex: 1; margin-top: 1.5rem; }
        .sidebar-link {
            display: flex; align-items: center; gap: 0.6rem;
            padding: 0.7rem 0.85rem; border-radius: 10px; color: #6f5a5d;
            text-decoration: none; margin-bottom: 0.3rem;
            transition: all 0.2s ease; border: 1px solid transparent;
            font-size: 0.875rem; font-weight: 500;
        }
        .sidebar-link:hover { background: rgba(244,219,227,0.55); color: var(--ink); }
        .sidebar-link.active {
            background: linear-gradient(90deg,rgba(244,219,227,0.95),rgba(239,209,221,0.9));
            border-color: rgba(143,103,113,0.15); color: var(--ink); font-weight: 600;
        }
        .sidebar-footer { border-top: 1px solid var(--line); padding-top: 1rem; margin-top: auto; }
        .main-area { display: flex; flex-direction: column; overflow: hidden; }
        .topbar {
            background: rgba(255,248,246,0.85); border-bottom: 1px solid var(--line);
            backdrop-filter: blur(10px); padding: 1rem 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
        }
        .page-title { font-family: 'Playfair Display', serif; font-size: 1.8rem; color: #7d5a62; margin: 0; line-height: 1.2; }
        .page-subtitle { color: var(--muted); font-size: 0.83rem; margin: 0; }
        .kds-body { padding: 1rem 1.25rem; flex: 1; overflow-y: auto; }
        .kanban-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1rem; }
        .kanban-col {
            background: rgba(255,250,248,0.65); border: 1px solid rgba(217,199,203,0.7);
            border-radius: 10px; padding: 1rem; min-height: 500px;
        }
        .col-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.9rem; }
        .col-title { font-family: 'Playfair Display', serif; font-size: 1.05rem; color: #79555c; font-weight: 600; }
        .col-chip { font-size: 0.67rem; text-transform: uppercase; letter-spacing: 0.08em; padding: 0.25rem 0.6rem; border-radius: 999px; background: #f1d7dd; color: #7f5a62; }
        .empty-state { text-align: center; color: var(--muted); font-size: 0.82rem; padding: 2rem 1rem; }
        .order-card {
            background: #fff; border: 1px solid var(--line); border-radius: 8px;
            padding: 0.85rem; margin-bottom: 0.75rem;
            box-shadow: 0 3px 10px rgba(102,72,78,0.06); transition: box-shadow 0.2s;
        }
        .order-card:hover { box-shadow: 0 6px 18px rgba(102,72,78,0.12); }
        .order-card.is-delivery { border-left: 4px solid #22c55e; }
        .order-card.is-pickup { border-left: 4px solid #8f6771; }
        .order-card.done { opacity: 0.65; }
        .card-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem; }
        .menu-name { font-family: 'Playfair Display', serif; font-size: 1rem; color: var(--ink); font-weight: 600; margin: 0; }
        .order-id { font-size: 0.72rem; font-weight: 700; color: #c0485a; white-space: nowrap; }
        .order-qty { font-size: 0.75rem; color: var(--muted); }
        .card-divider { border: none; border-top: 1px dashed var(--line); margin: 0.65rem 0; }
        .delivery-badge {
            display: flex; align-items: center; justify-content: center; gap: 0.4rem;
            padding: 0.3rem 0.65rem; border-radius: 999px;
            font-size: 0.74rem; font-weight: 600; width: 100%;
            margin-bottom: 0.5rem;
        }
        .delivery-badge.salon { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .delivery-badge.pickup { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }
        .guest-name { font-size: 0.76rem; color: var(--muted); margin-bottom: 0.55rem; }
        .btn-action {
            width: 100%; padding: 0.5rem; border-radius: 6px; border: none;
            font-weight: 700; font-size: 0.77rem; cursor: pointer;
            text-transform: uppercase; letter-spacing: 0.05em;
            transition: background-color 0.2s, transform 0.1s;
        }
        .btn-action:active { transform: scale(0.98); }
        .btn-start { background: var(--accent); color: #fff; }
        .btn-start:hover { background: #7f5a62; color: #fff; }
        .btn-deliver { background: #22c55e; color: #fff; }
        .btn-deliver:hover { background: #16a34a; color: #fff; }
        .btn-pickup-done { background: #fff; color: #6b7280; border: 1px solid #d9c7cb !important; }
        .btn-pickup-done:hover { background: #f9fafb; }
        .done-label { font-size: 0.73rem; font-weight: 600; color: #6b7280; text-align: center; margin-top: 0.35rem; }
        @media (max-width:991px) {
            .app-frame { grid-template-columns: 1fr; }
            .kanban-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="shell">
<div class="app-frame">
    <aside class="sidebar">
        <div>
            <img class="avatar" src="assets/merish_pictures/barista/Dimas.jpg" alt="Barista">
            <div class="brand-title">Merish<br>Barista</div>
            <div class="brand-subtitle">&#9679; Online</div>
        </div>
        <nav class="sidebar-nav">
            <a class="sidebar-link active" href="index.php?page=barista">
                <i class="fas fa-th-large fa-fw"></i> KDS Board
            </a>
            <a class="sidebar-link" href="index.php?page=barista&action=menuAvailability">
                <i class="fas fa-list-ul fa-fw"></i> Menu Availability
            </a>
            <a class="sidebar-link" href="index.php?page=barista&action=paymentCashier">
                <i class="fas fa-credit-card fa-fw"></i> Cafe Cashier
            </a>
        </nav>
        <div class="sidebar-footer">
            <a class="sidebar-link" style="color:#e74c3c;" href="<?= LOGOUT_URL ?>">
                <i class="fas fa-sign-out-alt fa-fw"></i> Logout
            </a>
        </div>
    </aside>

    <div class="main-area">
        <div class="topbar">
            <div>
                <h1 class="page-title">Live KDS Board</h1>
                <p class="page-subtitle">Monitor dan kelola pesanan minuman secara real-time.</p>
            </div>
            <div style="font-size:0.78rem;color:var(--muted);">
                <i class="fas fa-clock me-1"></i> <?php 
                $dt = new DateTime("now", new DateTimeZone("Asia/Jakarta"));
                echo $dt->format('d M Y, H:i') . ' WIB';
                ?>
            </div>
        </div>

        <div class="px-4 pt-2 pb-0 d-flex flex-wrap gap-3">
            <span style="font-size:0.76rem;color:var(--muted);display:flex;align-items:center;gap:0.35rem;">
                <span style="width:10px;height:10px;border-radius:2px;background:#22c55e;display:inline-block;"></span>
                Pelanggan Salon &rarr; Minuman <strong>Diantar</strong> ke kursi
            </span>
            <span style="font-size:0.76rem;color:var(--muted);display:flex;align-items:center;gap:0.35rem;">
                <span style="width:10px;height:10px;border-radius:2px;background:#8f6771;display:inline-block;"></span>
                Pelanggan Kafe &rarr; <strong>Pick Up</strong> / Ambil sendiri di bar
            </span>
        </div>

        <div class="kds-body">
            <div class="kanban-grid">

                <!-- COLUMN 1: NEW / PENDING -->
                <div class="kanban-col">
                    <div class="col-header">
                        <div class="col-title">Pesanan Masuk</div>
                        <span class="col-chip">Menunggu</span>
                    </div>
                    <?php
                    $hasPending = false;
                    foreach ($orders as $order):
                        $st = strtoupper((string)($order['STATUS'] ?? $order['status'] ?? ''));
                        if (!in_array($st, ['NEW','PENDING'], true)) continue;
                        $hasPending   = true;
                        $isSalon      = !empty($order['seat_id']);
                        $seatLabel    = $isSalon ? htmlspecialchars($order['seat_name'] ?? $order['seat_id']) : null;
                    ?>
                    <div class="order-card <?= $isSalon ? 'is-delivery' : 'is-pickup' ?>">
                        <div class="card-top">
                            <p class="menu-name"><?= htmlspecialchars($order['menu_name'] ?? 'Order') ?></p>
                            <div class="text-end">
                                <div class="order-id">#ORD-<?= str_pad((string)($order['order_id']??0),3,'0',STR_PAD_LEFT) ?></div>
                                <div class="order-qty"><?= (int)($order['qty']??1) ?>x</div>
                            </div>
                        </div>
                        <hr class="card-divider">
                        <?php if ($isSalon): ?>
                            <div class="delivery-badge salon">
                                <i class="fas fa-paper-plane"></i> Diantar ke <?= $seatLabel ?>
                            </div>
                        <?php else: ?>
                            <div class="delivery-badge pickup">
                                <i class="fas fa-shopping-bag"></i> Pick Up &mdash; Ambil di Bar
                            </div>
                        <?php endif; ?>
                        <div class="guest-name"><i class="fas fa-user fa-xs me-1"></i><?= htmlspecialchars($order['guest_name']??'Tamu') ?></div>
                        <button class="btn-action btn-start" onclick="updateOrderStatus(<?= (int)($order['order_id']??0) ?>,'In Progress')">
                            <i class="fas fa-fire me-1"></i> Mulai Buat
                        </button>
                    </div>
                    <?php endforeach; ?>
                    <?php if (!$hasPending): ?>
                        <div class="empty-state"><i class="fas fa-coffee fa-2x d-block mb-2" style="color:#d9c7cb;"></i>Belum ada pesanan masuk.</div>
                    <?php endif; ?>
                </div>

                <!-- COLUMN 2: IN PROGRESS -->
                <div class="kanban-col">
                    <div class="col-header">
                        <div class="col-title">Sedang Dibuat</div>
                        <span class="col-chip" style="background:#f5dce8;">Making</span>
                    </div>
                    <?php
                    $hasInProgress = false;
                    foreach ($orders as $order):
                        $st = strtoupper((string)($order['STATUS'] ?? $order['status'] ?? ''));
                        if (!in_array($st, ['IN PROGRESS','MAKING'], true)) continue;
                        $hasInProgress = true;
                        $isSalon       = !empty($order['seat_id']);
                        $seatLabel     = $isSalon ? htmlspecialchars($order['seat_name'] ?? $order['seat_id']) : null;
                    ?>
                    <div class="order-card <?= $isSalon ? 'is-delivery' : 'is-pickup' ?>">
                        <div class="card-top">
                            <p class="menu-name"><?= htmlspecialchars($order['menu_name'] ?? 'Order') ?></p>
                            <div class="text-end">
                                <div style="font-size:0.68rem;font-weight:700;color:#d97706;text-transform:uppercase;">Making</div>
                                <div class="order-id">#ORD-<?= str_pad((string)($order['order_id']??0),3,'0',STR_PAD_LEFT) ?></div>
                                <div class="order-qty"><?= (int)($order['qty']??1) ?>x</div>
                            </div>
                        </div>
                        <hr class="card-divider">
                        <?php if ($isSalon): ?>
                            <div class="delivery-badge salon">
                                <i class="fas fa-paper-plane"></i> Selesai &rarr; Antar ke <?= $seatLabel ?>
                            </div>
                        <?php else: ?>
                            <div class="delivery-badge pickup">
                                <i class="fas fa-shopping-bag"></i> Selesai &rarr; Pelanggan Ambil Sendiri
                            </div>
                        <?php endif; ?>
                        <div class="guest-name"><i class="fas fa-user fa-xs me-1"></i><?= htmlspecialchars($order['guest_name']??'Tamu') ?></div>
                        <?php if ($isSalon): ?>
                            <button class="btn-action btn-deliver" onclick="updateOrderStatus(<?= (int)($order['order_id']??0) ?>,'Ready')">
                                <i class="fas fa-paper-plane me-1"></i> Selesai &amp; Siap Diantar
                            </button>
                        <?php else: ?>
                            <button class="btn-action btn-pickup-done" onclick="updateOrderStatus(<?= (int)($order['order_id']??0) ?>,'Ready')">
                                <i class="fas fa-bell me-1"></i> Selesai &amp; Siap Diambil
                            </button>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                    <?php if (!$hasInProgress): ?>
                        <div class="empty-state"><i class="fas fa-mug-hot fa-2x d-block mb-2" style="color:#d9c7cb;"></i>Tidak ada yang sedang dibuat.</div>
                    <?php endif; ?>
                </div>

                <!-- COLUMN 3: READY / DONE -->
                <div class="kanban-col">
                    <div class="col-header">
                        <div class="col-title">Selesai</div>
                        <span class="col-chip" style="background:#eee5e8;">Delivered</span>
                    </div>
                    <?php
                    $hasDone = false;
                    foreach ($orders as $order):
                        $st = strtoupper((string)($order['STATUS'] ?? $order['status'] ?? ''));
                        if (!in_array($st, ['READY','SELESAI','DONE','COMPLETED'], true)) continue;
                        $hasDone   = true;
                        $isSalon   = !empty($order['seat_id']);
                        $seatLabel = $isSalon ? htmlspecialchars($order['seat_name'] ?? $order['seat_id']) : null;
                    ?>
                    <div class="order-card done <?= $isSalon ? 'is-delivery' : 'is-pickup' ?>">
                        <div class="card-top">
                            <p class="menu-name"><?= htmlspecialchars($order['menu_name'] ?? 'Order') ?></p>
                            <div class="text-end">
                                <div class="order-id">#ORD-<?= str_pad((string)($order['order_id']??0),3,'0',STR_PAD_LEFT) ?></div>
                                <div class="order-qty"><?= (int)($order['qty']??1) ?>x</div>
                            </div>
                        </div>
                        <hr class="card-divider">
                        <?php if ($isSalon): ?>
                            <div class="delivery-badge salon">
                                <i class="fas fa-check-circle"></i> Sudah diantar ke <?= $seatLabel ?>
                            </div>
                        <?php else: ?>
                            <div class="delivery-badge pickup">
                                <i class="fas fa-check-circle"></i> Sudah diambil pelanggan
                            </div>
                        <?php endif; ?>
                        <div class="guest-name"><i class="fas fa-user fa-xs me-1"></i><?= htmlspecialchars($order['guest_name']??'Tamu') ?></div>
                        <div class="done-label"><i class="fas fa-check me-1"></i><?= ucfirst(strtolower($st)) ?></div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (!$hasDone): ?>
                        <div class="empty-state"><i class="fas fa-check-circle fa-2x d-block mb-2" style="color:#d9c7cb;"></i>Belum ada pesanan selesai.</div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>
</div>

<script>
function updateOrderStatus(orderId, newStatus) {
    const params = new URLSearchParams();
    params.append('order_id', orderId);
    params.append('status', newStatus);
    fetch('index.php?page=barista&action=updateOrderStatus', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: params.toString()
    })
    .then(res => res.json().catch(() => ({success:false,message:'Invalid response'})))
    .then(data => {
        if (data && data.success) { location.reload(); }
        else { alert(data.message || 'Gagal mengubah status pesanan'); }
    })
    .catch(() => alert('Network error'));
}
</script>
</body>
</html>
