ï»¿<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barista Dashboard - Merish System</title>
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
        .quick-btn {
            border: 1px solid var(--line); background: #fff; color: #5e4a4d;
            border-radius: 8px; padding: 0.55rem 0.9rem; font-size: 0.8rem;
            letter-spacing: 0.06em; text-transform: uppercase;
        }
        .panel {
            background: rgba(255, 250, 248, 0.76); border: 1px solid var(--line);
            border-radius: 10px; box-shadow: 0 8px 24px rgba(118, 83, 89, 0.06);
        }
        .column-title {
            font-family: 'Playfair Display', serif; font-size: 1.45rem; color: #79555c;
        }
        .status-chip {
            font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.08em;
            padding: 0.3rem 0.55rem; border-radius: 999px; background: #f1d7dd; color: #7f5a62;
        }
        .order-card {
            background: #fff; border: 1px solid var(--line); border-left: 3px solid #8f6771;
            border-radius: 6px; box-shadow: 0 6px 14px rgba(102, 72, 78, 0.06);
        }
        .order-card-title {
            font-family: 'Playfair Display', serif; font-size: 1.1rem; color: var(--ink);
        }
        .order-muted { color: var(--muted); font-size: 0.88rem; }
        .action-primary { background: var(--accent); border-color: var(--accent); color: #fff; }
        .action-primary:hover { background: #7f5a62; border-color: #7f5a62; color: #fff; }
        .action-secondary { background: #fff; border: 1px solid #d9c7cb; color: #7a6165; }
        .kanban-column {
            min-height: 520px; background: rgba(255, 250, 248, 0.6);
            border: 1px solid rgba(217, 199, 203, 0.75); border-radius: 8px;
        }
        @media (max-width: 991.98px) {
            .sidebar { min-height: auto; }
        }
    </style>
</head>
<body>
<div class="shell">
    <div class="app-frame row g-0">
        <aside class="col-lg-2 sidebar d-flex flex-column">
            <div class="p-4">
                <div class="d-flex justify-content-center mb-3">
                    <img class="avatar" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=240&q=80" alt="Barista profile">
                </div>
                <div class="text-center mb-4">
                    <div class="brand-title">Merish<br>Barista</div>
                    <div class="brand-subtitle mt-2">Online</div>
                </div>
                <a class="sidebar-link active" href="index.php?page=barista">
                    <span>â˜•</span>
                    <span>KDS Board</span>
                </a>
                <a class="sidebar-link" href="index.php?page=barista&action=menuAvailability">
                    <span>â˜°</span>
                    <span>Menu Availability</span>
                </a>
            </div>
            <div class="mt-auto p-4 sidebar-footer">
                <a class="sidebar-link" href="<?= LOGOUT_URL ?>">
                    <span>âš™</span>
                    <span>Settings</span>
                </a>
                <a class="sidebar-link" href="<?= LOGOUT_URL ?>">
                    <span>?</span>
                    <span>Support</span>
                </a>
            </div>
        </aside>

        <section class="col-lg-10 d-flex flex-column">
            <div class="main-topbar px-4 px-lg-5 py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="page-title">Live KDS Board</h1>
                    <p class="page-subtitle">Manage salon beverage orders in real-time.</p>
                </div>
                <button class="quick-btn">Quick Menu</button>
            </div>

            <div class="p-4 p-lg-5 flex-grow-1">
                <div class="panel p-3 p-lg-4">
                    <div class="row g-3">
                        <div class="col-lg-4">
                            <div class="kanban-column p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="column-title" style="font-size:1.2rem;">New Orders</div>
                                    <span class="status-chip">Pending</span>
                                </div>

                                <?php $hasPending = false; ?>
                                <?php foreach ($orders as $order): ?>
                                    <?php $status = strtoupper((string)($order['STATUS'] ?? $order['status'] ?? '')); ?>
                                    <?php if (in_array($status, ['NEW', 'PENDING'], true)): ?>
                                        <?php $hasPending = true; ?>
                                        <div class="order-card p-3 mb-3">
                                            <div class="d-flex justify-content-between gap-3">
                                                <div>
                                                    <div class="order-card-title"><?= htmlspecialchars($order['menu_name'] ?? 'Order') ?></div>
                                                    <div class="order-muted"><?= htmlspecialchars((string)($order['status'] ?? $order['STATUS'] ?? 'Pending')) ?></div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="small text-danger fw-semibold">#ORD-<?= str_pad((string)($order['order_id'] ?? 0), 3, '0', STR_PAD_LEFT) ?></div>
                                                    <div class="small order-muted"><?= htmlspecialchars((string)($order['qty'] ?? 1)) ?>x</div>
                                                </div>
                                            </div>
                                            <hr class="my-3">
                                            <div class="small order-muted mb-3">Reservation <?= htmlspecialchars((string)($order['res_id'] ?? '-')) ?></div>
                                            <button class="btn action-primary w-100" onclick="updateOrderStatus(<?= (int)($order['order_id'] ?? 0) ?>, 'In Progress')">Proses Pesanan</button>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                                <?php if (!$hasPending): ?>
                                    <div class="text-muted small">No new orders.</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="kanban-column p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="column-title" style="font-size:1.2rem;">In Progress</div>
                                    <span class="status-chip" style="background:#f5dce8;">Making</span>
                                </div>

                                <?php $hasInProgress = false; ?>
                                <?php foreach ($orders as $order): ?>
                                    <?php $status = strtoupper((string)($order['STATUS'] ?? $order['status'] ?? '')); ?>
                                    <?php if (in_array($status, ['IN PROGRESS', 'MAKING'], true)): ?>
                                        <?php $hasInProgress = true; ?>
                                        <div class="order-card p-3 mb-3">
                                            <div class="d-flex justify-content-between gap-3">
                                                <div>
                                                    <div class="order-card-title"><?= htmlspecialchars($order['menu_name'] ?? 'Order') ?></div>
                                                    <div class="order-muted">Standard recipe.</div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="small text-muted fw-semibold">MAKING</div>
                                                    <div class="small order-muted">#ORD-<?= str_pad((string)($order['order_id'] ?? 0), 3, '0', STR_PAD_LEFT) ?></div>
                                                </div>
                                            </div>
                                            <hr class="my-3">
                                            <div class="small order-muted mb-3">Reservation <?= htmlspecialchars((string)($order['res_id'] ?? '-')) ?></div>
                                            <button class="btn action-secondary w-100" onclick="updateOrderStatus(<?= (int)($order['order_id'] ?? 0) ?>, 'Selesai')">Selesai & Antar</button>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                                <?php if (!$hasInProgress): ?>
                                    <div class="text-muted small">No orders in progress.</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="kanban-column p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="column-title" style="font-size:1.2rem;">Done</div>
                                    <span class="status-chip" style="background:#eee5e8;">Recently Finished</span>
                                </div>

                                <?php $hasDone = false; ?>
                                <?php foreach ($orders as $order): ?>
                                    <?php $status = strtoupper((string)($order['STATUS'] ?? $order['status'] ?? '')); ?>
                                    <?php if (in_array($status, ['SELESAI', 'DONE', 'COMPLETED'], true)): ?>
                                        <?php $hasDone = true; ?>
                                        <div class="order-card p-3 mb-3 opacity-75">
                                            <div class="d-flex justify-content-between gap-3">
                                                <div>
                                                    <div class="order-card-title"><?= htmlspecialchars($order['menu_name'] ?? 'Order') ?></div>
                                                    <div class="order-muted">Delivered to customer</div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="small text-muted fw-semibold">#ORD-<?= str_pad((string)($order['order_id'] ?? 0), 3, '0', STR_PAD_LEFT) ?></div>
                                                </div>
                                            </div>
                                            <hr class="my-3">
                                            <div class="small order-muted">Qty <?= htmlspecialchars((string)($order['qty'] ?? 1)) ?> â€¢ Reservation <?= htmlspecialchars((string)($order['res_id'] ?? '-')) ?></div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                                <?php if (!$hasDone): ?>
                                    <div class="text-muted small">No completed orders yet.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
function updateOrderStatus(orderId, newStatus) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'index.php?page=barista&action=updateOrderStatus';

    const orderInput = document.createElement('input');
    orderInput.type = 'hidden';
    orderInput.name = 'order_id';
    orderInput.value = orderId;

    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'status';
    statusInput.value = newStatus;

    form.appendChild(orderInput);
    form.appendChild(statusInput);
    document.body.appendChild(form);
    form.submit();
}
</script>
</body>
</html>



