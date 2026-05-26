<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Achievements - Merish System</title>
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
        .hero-title { font-family:'Playfair Display',serif; font-size:clamp(2rem,3vw,3.4rem); color:#231d1e; }
        .card-title-hero { font-family:'Playfair Display',serif; font-size:1.65rem; color:#483437; }
        .stat-card { background:#fff; border:1px solid var(--line); border-radius:10px; }
        .stat-label { color:var(--muted); letter-spacing:.08em; text-transform:uppercase; font-size:.72rem; }
        .stat-value { font-family:'Playfair Display',serif; font-size:2rem; color:#5a4147; }
        .review-card { background:#fff; border:1px solid var(--line); border-radius:10px; }
        .quote { color:#6f5b61; font-style:italic; }
        .badge-soft { background:#f3e2e8; color:#7d5a62; letter-spacing:.08em; text-transform:uppercase; font-size:.7rem; }
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
                <a class="sidebar-link" href="index.php?page=beautician&action=schedule"><span>◷</span><span>Schedule</span></a>
                <a class="sidebar-link" href="index.php?page=beautician&action=treatments"><span>✦</span><span>Treatments</span></a>
                <a class="sidebar-link active" href="index.php?page=beautician&action=achievements"><span>⌁</span><span>Achievements</span></a>
            </div>
            <div class="mt-auto p-4 sidebar-footer">
                <a class="sidebar-link" href="index.php?page=login&action=logout"><span>⚙</span><span>Settings</span></a>
            </div>
        </aside>
        <section class="col-lg-10 d-flex flex-column">
            <div class="main-topbar px-4 px-lg-5 py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="page-title"><?= htmlspecialchars($pageTitle ?? 'Achievements') ?></h1>
                    <p class="page-subtitle">A curated overview of your professional impact and client satisfaction metrics.</p>
                </div>
                <button class="quick-btn">View All Reviews</button>
            </div>
            <div class="p-4 p-lg-5 flex-grow-1">
                <div class="panel p-4 p-lg-5">
                    <div class="row g-4">
                        <div class="col-lg-7">
                            <div class="row g-3 mb-4">
                                <?php foreach (($stats ?? []) as $stat): ?>
                                    <div class="col-md-4">
                                        <div class="stat-card p-4 h-100">
                                            <div class="stat-label"><?= htmlspecialchars($stat['label']) ?></div>
                                            <div class="stat-value mt-2"><?= htmlspecialchars($stat['value']) ?></div>
                                            <div class="text-muted small mt-1"><?= htmlspecialchars($stat['hint']) ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="review-card p-4">
                                <div class="card-title-hero mb-4">Clients Served This Week</div>
                                <div class="panel p-4" style="background:#fff;">
                                    <div class="d-flex justify-content-between align-items-end" style="height:220px;">
                                        <?php for ($i = 0; $i < 6; $i++): ?>
                                            <div class="text-center flex-fill">
                                                <div style="height:<?= 25 + ($i * 14) ?>px; width:10px; margin:0 auto 12px; border-radius:999px; background:#8a6170; opacity:0.2;"></div>
                                                <div class="small text-muted"><?= ['Mon','Tue','Wed','Thu','Fri','Sat'][$i] ?></div>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="card-title-hero mb-3">Recent Client Praise</div>
                            <div class="d-grid gap-3">
                                <?php foreach (($reviews ?? []) as $review): ?>
                                    <div class="review-card p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <div class="small text-uppercase fw-semibold" style="letter-spacing:.08em; color:#6f5a61;"><?= htmlspecialchars($review['name']) ?></div>
                                                <div class="small text-muted mt-1"><?= htmlspecialchars($review['tag']) ?></div>
                                            </div>
                                            <div style="color:#c8a9c9;">★★★★★</div>
                                        </div>
                                        <div class="quote">“<?= htmlspecialchars($review['text']) ?>”</div>
                                        <div class="mt-3"><span class="badge badge-soft"><?= htmlspecialchars($review['tag']) ?></span></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
</body>
</html>
