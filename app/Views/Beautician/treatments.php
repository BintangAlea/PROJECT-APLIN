<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treatments - Merish System</title>
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
        .sub-italic { color:var(--muted); font-style:italic; border-left:2px solid #e8c7d0; padding-left:12px; }
        .treat-card { background:#fff; border:1px solid var(--line); border-radius:8px; box-shadow:0 6px 16px rgba(95,68,74,.05); }
        .treatment-name { font-family:'Playfair Display',serif; font-size:1.45rem; color:#2d2325; }
        .material-box { background:#fff9fc; border:1px solid #ecd8dd; border-radius:10px; }
        .material-row { border-top:1px solid #f0e2e5; }
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
                <a class="sidebar-link active" href="index.php?page=beautician&action=treatments"><span>✦</span><span>Treatments</span></a>
                <a class="sidebar-link" href="index.php?page=beautician&action=achievements"><span>⌁</span><span>Achievements</span></a>
            </div>
            <div class="mt-auto p-4 sidebar-footer">
                <a class="sidebar-link" href="index.php?page=beautician&action=settings"><span>⚙</span><span>Settings</span></a>
            </div>
        </aside>
        <section class="col-lg-10 d-flex flex-column">
            <div class="main-topbar px-4 px-lg-5 py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="page-title"><?= htmlspecialchars($pageTitle ?? 'Treatments') ?></h1>
                    <p class="page-subtitle">Review service details and material usage.</p>
                </div>
                <a class="quick-btn" href="index.php?page=beautician&action=settings" style="text-decoration:none; display:inline-flex; align-items:center;">Save Draft</a>
            </div>
            <div class="p-4 p-lg-5 flex-grow-1">
                <div class="panel p-4 p-lg-5">
                    <div class="row g-4">
                        <div class="col-lg-7">
                            <div class="small text-uppercase text-muted mb-2">Treatment Detail</div>
                            <div class="hero-title">Client 'Victoria Sterling'</div>
                            <div class="sub-italic mt-2 mb-4">Luminous Balayage</div>

                            <div class="treat-card p-4 mb-4">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <span class="badge-soft badge">Service Information</span>
                                </div>
                                <?php foreach (($treatments ?? []) as $index => $treatment): ?>
                                    <div class="d-flex gap-3 mb-4">
                                        <div class="badge rounded-pill border border-1" style="color:#7d5a62; border-color:#d9c7cb !important; min-width: 2rem; height: 2rem; display:flex; align-items:center; justify-content:center;"><?= str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) ?></div>
                                        <div>
                                            <div class="treatment-name"><?= htmlspecialchars($treatment['name']) ?></div>
                                            <div class="text-muted"><?= htmlspecialchars($treatment['subtitle']) ?></div>
                                            <div class="small mt-2" style="color:#6f5b61; max-width: 560px;"><?= htmlspecialchars($treatment['note']) ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="treat-card p-4">
                                <div class="small text-uppercase text-muted mb-2">Reception Notes</div>
                                <div class="p-4" style="border:1px solid #eddde1; border-radius:8px; min-height:160px; color:#c1a7ab; background:#fff9fb;">e.g. 'Client requested extra hair vitamin, please add to final bill...'</div>
                                <div class="text-end small text-uppercase text-muted mt-2">Visible to front desk</div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="material-box p-4 h-100">
                                <div class="text-center mb-4">
                                    <div class="section-title" style="font-family:'Playfair Display',serif; font-size:1.8rem; color:#6d4f58;">Material Usage</div>
                                    <div class="text-muted">Standard Bill of Materials</div>
                                </div>
                                <div class="mb-3">
                                    <?php foreach (($materials ?? []) as $material): ?>
                                        <div class="d-flex justify-content-between align-items-center py-3 material-row">
                                            <div class="text-muted"><?= htmlspecialchars($material['name']) ?></div>
                                            <span class="badge badge-soft"><?= htmlspecialchars($material['qty']) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="mt-4 pt-4 border-top border-1 text-center" style="border-color:#eddde1 !important;">
                                    <div class="section-title" style="font-family:'Playfair Display',serif; font-size:1.6rem; color:#8c6d76;">L'Éclat Signature Service</div>
                                </div>
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
