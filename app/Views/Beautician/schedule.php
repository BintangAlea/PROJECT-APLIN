<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule - Merish System</title>
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
        .section-title { font-family:'Playfair Display',serif; font-size:1.6rem; color:#483437; }
        .timeline { position:relative; padding-left:1.5rem; }
        .timeline:before { content:''; position:absolute; left:.5rem; top:0; bottom:0; width:1px; background:var(--line); }
        .time-dot { position:absolute; left:.35rem; width:.45rem; height:.45rem; border-radius:50%; background:#8a6170; transform:translateY(.45rem); }
        .schedule-card { background:#fff; border:1px solid var(--line); border-radius:8px; box-shadow:0 6px 16px rgba(95,68,74,.05); }
        .name { font-family:'Playfair Display',serif; font-size:1.45rem; color:#2d2325; }
        .note-box { background:#fdf8f8; border:1px solid #ecd8dd; border-radius:8px; color:#7d6268; font-size:.92rem; }
        .badge-soft { background:#f3e2e8; color:#7d5a62; letter-spacing:.08em; text-transform:uppercase; font-size:.7rem; }
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
                <a class="sidebar-link" href="index.php?page=beautician"><span>◫</span><span>Dashboard</span></a>
                <a class="sidebar-link active" href="index.php?page=beautician&action=schedule"><span>◷</span><span>Schedule</span></a>
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
                    <h1 class="page-title"><?= htmlspecialchars($scheduleHeading ?? $pageTitle ?? 'Schedule') ?></h1>
                    <p class="page-subtitle">Manage your appointments and daily flow.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a class="quick-btn quick-link" href="index.php?page=beautician&action=todaySchedule">Today</a>
                    <a class="quick-btn quick-link" href="index.php?page=beautician&action=upcomingSchedule&days=14">Weekly</a>
                    <a class="quick-btn quick-link" href="index.php?page=beautician&action=settings">Settings</a>
                </div>
            </div>
            <div class="p-4 p-lg-5 flex-grow-1">
                <div class="panel p-4 p-lg-5">
                    <div class="section-title mb-4"><?= htmlspecialchars($scheduleHeading ?? "Today's Schedule") ?></div>
                    <div class="timeline">
                        <?php $items = $todaySchedule ?? []; ?>
                        <?php if (!empty($items)): ?>
                            <?php foreach ($items as $item): ?>
                                <div class="position-relative mb-4">
                                    <div class="time-dot"></div>
                                    <div class="row g-3 align-items-center">
                                        <div class="col-md-2 text-muted small">
                                            <div><?= htmlspecialchars($item['reservation_time'] ?? '--:--') ?></div>
                                            <div><?= htmlspecialchars($item['reservation_date'] ?? '') ?></div>
                                        </div>
                                        <div class="col-md-10">
                                            <div class="schedule-card p-4 d-flex justify-content-between align-items-start gap-3">
                                                <div>
                                                    <div class="small text-uppercase text-muted mb-2">Current Task</div>
                                                    <div class="name"><?= htmlspecialchars($item['customer_name'] ?? 'Client') ?></div>
                                                    <div class="mt-2 text-muted"><?= htmlspecialchars($item['service_name'] ?? 'Treatment') ?></div>
                                                    <div class="note-box mt-3 p-3">Seat: <?= htmlspecialchars($item['seat_name'] ?? 'N/A') ?> • Status: <?= htmlspecialchars($item['status'] ?? 'Pending') ?></div>
                                                </div>
                                                <div class="d-flex flex-column gap-2">
                                                    <?php if (!in_array((string) ($item['status'] ?? ''), ['Completed', 'Selesai'], true)): ?>
                                                        <form method="POST" action="index.php?page=beautician&action=updateReservationStatus" class="m-0">
                                                            <input type="hidden" name="reservation_id" value="<?= (int) ($item['res_id'] ?? 0) ?>">
                                                            <input type="hidden" name="status" value="In-Service">
                                                            <button class="btn btn-outline-secondary px-4 w-100" type="submit">Start Treatment</button>
                                                        </form>
                                                        <form method="POST" action="index.php?page=beautician&action=updateReservationStatus" class="m-0">
                                                            <input type="hidden" name="reservation_id" value="<?= (int) ($item['res_id'] ?? 0) ?>">
                                                            <input type="hidden" name="status" value="Selesai">
                                                            <button class="btn btn-primary px-4 w-100" type="submit" style="background:#8a6170;border-color:#8a6170;">Complete Service</button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="badge badge-soft px-3 py-2">Completed</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-muted">Tidak ada booking untuk hari ini.</div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-5 mb-3">
                        <div class="section-title">Upcoming Appointments</div>
                        <span class="badge badge-soft"><?= htmlspecialchars($upcomingLabel ?? 'Next 14 Days') ?></span>
                    </div>
                    <div class="row g-3">
                        <?php $upcoming = $upcomingSchedule ?? []; ?>
                        <?php if (!empty($upcoming)): ?>
                            <?php foreach (array_slice($upcoming, 0, 2) as $item): ?>
                                <div class="col-lg-6">
                                    <div class="schedule-card p-4 h-100">
                                        <div class="small text-muted"><?= htmlspecialchars($item['reservation_time'] ?? '') ?></div>
                                        <div class="name"><?= htmlspecialchars($item['customer_name'] ?? 'Client') ?></div>
                                        <div class="mt-2 text-muted"><?= htmlspecialchars($item['service_name'] ?? 'Service') ?></div>
                                        <div class="mt-3"><span class="badge badge-soft"><?= htmlspecialchars($item['seat_name'] ?? 'Seat') ?></span></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12"><div class="schedule-card p-4 text-muted">No upcoming appointments yet.</div></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
</body>
</html>
