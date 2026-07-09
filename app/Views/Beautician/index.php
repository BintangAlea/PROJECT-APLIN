<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beautician Dashboard - Merish System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg:#fbf2f1; --panel:#fff9f8; --line:#eadad6; --ink:#37282c; --muted:#8e7377; --accent:#8a6170; }
        body { margin:0; background: radial-gradient(circle at top left, #232323 0, #141414 24%, #0f0f0f 100%); font-family:'Inter',sans-serif; color:var(--ink); }
        .shell { min-height:100vh; padding:10px; }
        .app-frame { min-height:calc(100vh - 20px); border-radius:14px; overflow:hidden; background:var(--bg); box-shadow:0 20px 60px rgba(0,0,0,.35); }
        .sidebar { background:rgba(249,240,239,.94); border-right:1px solid var(--line); min-height:calc(100vh - 20px); }
        .brand-title { font-family:'Playfair Display',serif; font-size:2.35rem; line-height:.96; color:#6f4e56; }
        .brand-subtitle { color:var(--muted); font-size:.85rem; }
        .avatar { width:60px; height:60px; border-radius:50%; object-fit:cover; border:2px solid #f0ddd9; background:#f0ddd9; }
        .sidebar-link { display:flex; align-items:center; gap:.7rem; padding:.9rem 1rem; border-radius:10px; color:#6d5a5c; text-decoration:none; margin-bottom:.35rem; transition:.2s ease; border:1px solid transparent; letter-spacing:.06em; }
        .sidebar-link:hover { background:rgba(244,219,227,.56); color:var(--ink); }
        .sidebar-link.active { background:linear-gradient(90deg, rgba(244,219,227,.96), rgba(239,209,221,.9)); border-color:rgba(143,103,113,.15); color:var(--ink); }
        .sidebar-footer { border-top:1px solid var(--line); }
        .main-topbar { background:rgba(255,248,246,.8); border-bottom:1px solid var(--line); backdrop-filter:blur(10px); }
        .page-title { font-family:'Playfair Display',serif; font-size:clamp(2rem,3vw,3rem); color:#7d5a62; margin-bottom:0; }
        .page-subtitle { color:var(--muted); margin-bottom:0; }
        .quick-btn { border:1px solid var(--line); background:#fff; color:#5e4a4d; border-radius:8px; padding:.55rem .9rem; font-size:.8rem; letter-spacing:.06em; text-transform:uppercase; }
        .panel { background:rgba(255,250,248,.76); border:1px solid var(--line); border-radius:10px; box-shadow:0 8px 24px rgba(118,83,89,.06); }
        .hero-card { overflow:hidden; min-height:320px; }
        .hero-image { background:linear-gradient(135deg, rgba(34,34,34,.95), rgba(15,15,15,.96)); min-height:320px; display:flex; align-items:center; justify-content:center; color:rgba(255,255,255,.2); font-family:'Playfair Display',serif; font-size:4rem; }
        .status-chip { display:inline-block; font-size:.72rem; letter-spacing:.08em; text-transform:uppercase; padding:.35rem .6rem; border-radius:999px; background:#f1d7dd; color:#7d5962; }
        .task-title { font-family:'Playfair Display',serif; font-size:clamp(2rem,3vw,3.2rem); color:#231d1e; }
        .task-sub { color:var(--muted); font-style:italic; border-left:2px solid #e8c7d0; padding-left:12px; }
        .mini-card { background:#fff; border:1px solid var(--line); border-radius:8px; box-shadow:0 6px 16px rgba(95,68,74,.05); }
        .mini-name { font-family:'Playfair Display',serif; color:#2d2325; font-size:1.25rem; }
        .mini-meta { color:var(--muted); font-size:.88rem; }
        .section-title { font-family:'Playfair Display',serif; font-size:1.6rem; color:#483437; }
        .view-link { color:var(--muted); text-decoration:none; font-size:.85rem; }
        .view-link:hover { color:var(--ink); }
        .quick-link { text-decoration:none; display:inline-flex; align-items:center; }
        @media (max-width:991.98px){ .sidebar{ min-height:auto; } }
    </style>
</head>
<body>
<div class="shell">
    <div class="app-frame row g-0">
        <aside class="col-lg-2 sidebar d-flex flex-column">
            <div class="p-4">
                <div class="text-center mb-4"><div class="brand-title">L'Éclat<br>Management</div></div>
                <div class="d-flex align-items-center gap-3 mb-4">
                    <img class="avatar" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=240&q=80" alt="Beautician profile">
                    <div>
                        <div class="fw-semibold" style="font-size:1.05rem;"><?= htmlspecialchars($_SESSION['full_name'] ?? 'Beautician') ?></div>
                        <div class="brand-subtitle">Senior Esthetician</div>
                        <div class="brand-subtitle"><span style="color:#7c5d66;">●</span> Online</div>
                    </div>
                </div>
                <a class="sidebar-link active" href="index.php?page=beautician"><span>◫</span><span>Dashboard</span></a>
                <a class="sidebar-link" href="index.php?page=beautician&action=schedule"><span>◷</span><span>Schedule</span></a>
                <a class="sidebar-link" href="index.php?page=beautician&action=treatments"><span>✦</span><span>Treatments</span></a>
                <a class="sidebar-link" href="index.php?page=beautician&action=achievements"><span>⌁</span><span>Achievements</span></a>
            </div>
            <div class="mt-auto p-4 sidebar-footer">
                <a class="sidebar-link" href="index.php?page=beautician&action=settings"><span>⚙</span><span>Settings</span></a>
            </div>
        </aside>

        <section class="col-lg-10 d-flex flex-column">
            <div class="main-topbar px-4 px-lg-5 py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="page-title">Dashboard</h1>
                    <p class="page-subtitle">Manage your appointments and daily flow.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a class="quick-btn quick-link" href="index.php?page=beautician&action=todaySchedule">Today</a>
                    <a class="quick-btn quick-link" href="index.php?page=beautician&action=upcomingSchedule&days=14">Weekly</a>
                    <a class="quick-btn quick-link" href="index.php?page=booking">New Appointment</a>
                </div>
            </div>

            <div class="p-4 p-lg-5 flex-grow-1">
                <?php $currentTask = $todaySchedule[0] ?? null; ?>
                <div class="panel hero-card mb-4">
                    <div class="row g-0 h-100">
                        <div class="col-lg-4">
                            <div class="hero-image">✨</div>
                        </div>
                        <div class="col-lg-8 p-4 p-lg-5 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <div class="small text-uppercase" style="color:#9b7581; letter-spacing:0.12em;">Current Task</div>
                                        <span class="status-chip mt-2">In Progress</span>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-semibold" style="font-size:1.4rem; color:#7b5a62;">45:00</div>
                                        <div class="small text-muted">Remaining</div>
                                    </div>
                                </div>
                                <div class="small text-muted mb-2">Salon Seat 2</div>
                                <div class="task-title"><?= htmlspecialchars($currentTask['customer_name'] ?? $_SESSION['full_name'] ?? 'Victoria Sterling') ?></div>
                                <div class="task-sub mt-3"><?= htmlspecialchars($currentTask['service_name'] ?? 'Luminous Balayage & Olaplex Treatment') ?></div>
                            </div>
                            <div class="d-flex gap-2 mt-4">
                                <?php if (!empty($currentTask['res_id'])): ?>
                                    <form method="POST" action="index.php?page=beautician&action=updateReservationStatus" class="m-0 d-inline">
                                        <input type="hidden" name="reservation_id" value="<?= (int) $currentTask['res_id'] ?>">
                                        <input type="hidden" name="status" value="Selesai">
                                        <button class="btn btn-primary px-4" type="submit" style="background:#8a6170;border-color:#8a6170;">Complete Service</button>
                                    </form>
                                <?php else: ?>
                                    <a href="index.php?page=beautician&action=schedule" class="btn btn-primary px-4" style="background:#8a6170;border-color:#8a6170;">View Schedule</a>
                                <?php endif; ?>
                                <a href="index.php?page=beautician&action=settings" class="btn btn-outline-secondary px-4 text-decoration-none">Settings</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="section-title">Next Appointments</div>
                    <a href="index.php?page=beautician&action=schedule" class="view-link">View Schedule</a>
                </div>

                <div class="row g-3">
                    <?php $cards = array_slice($upcomingSchedule ?? [], 0, 2); ?>
                    <?php if (!empty($cards)): ?>
                        <?php foreach ($cards as $card): ?>
                            <div class="col-lg-6">
                                <div class="mini-card p-3 p-lg-4 h-100">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="small text-muted"><?= htmlspecialchars($card['reservation_time'] ?? '--:--') ?></div>
                                            <div class="mini-name"><?= htmlspecialchars($card['customer_name'] ?? 'Client') ?></div>
                                            <div class="mini-meta mt-2"><?= htmlspecialchars($card['service_name'] ?? 'Signature treatment') ?></div>
                                        </div>
                                        <div class="text-end">
                                            <span class="status-chip" style="background:#f3e2e8;">Seat</span>
                                            <div class="small text-muted mt-2"><?= htmlspecialchars($card['seat_name'] ?? 'N/A') ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="mini-card p-4 text-muted">No upcoming appointments yet.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</div>
</body>
</html>


