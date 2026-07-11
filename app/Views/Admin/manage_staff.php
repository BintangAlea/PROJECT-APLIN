<?php
$pageTitle = 'Staff & Review - Merish Admin';
$staff = $staff ?? [];
$reviews = $reviews ?? [];
$roleSummary = $roleSummary ?? ['Receptionist' => 0, 'Barista' => 0, 'Beautician' => 0];
$totalStaff = $totalStaff ?? count($staff);
$topPerformer = $topPerformer ?? null;
$activeTab = $activeTab ?? 'staff';
$flashSuccess = $_SESSION['success'] ?? null;
$flashError = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$formatRating = static fn ($value): string => number_format((float) $value, 1);
$roleLabel = static function (string $role): string {
    return match ($role) {
        'Beautician' => 'Beauty Specialist',
        'Barista' => 'Lead Barista',
        default => 'Front Desk',
    };
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $escape($pageTitle); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #fbf1ef;
            --panel: #ffffff;
            --line: #eadfdc;
            --ink: #513f46;
            --muted: #88787d;
            --accent: #8b6472;
            --accent-dark: #764f5d;
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: 'Montserrat', sans-serif;
        }

        .shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 250px minmax(0, 1fr);
        }

        .sidebar {
            background: #f8efed;
            border-right: 1px solid #eadfdc;
            padding: 1.4rem 1.1rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .brand {
            font-family: 'Playfair Display', serif;
            color: var(--accent);
            font-size: 3rem;
            line-height: 1;
            text-decoration: none;
        }

        .brand-sub {
            color: #7e6d72;
            font-size: 0.84rem;
            letter-spacing: 0.6px;
        }

        .new-booking {
            background: var(--accent);
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 0.72rem;
            font-weight: 700;
            border: 0;
            padding: 0.85rem 1rem;
            text-decoration: none;
            display: inline-flex;
            justify-content: center;
            align-items: center;
        }

        .new-booking:hover {
            background: var(--accent-dark);
            color: #fff;
        }

        .side-nav .nav-link {
            color: #5c4d53;
            padding: 0.8rem 0.9rem;
            border-radius: 0;
            font-size: 0.92rem;
        }

        .side-nav .nav-link.active,
        .side-nav .nav-link:hover {
            background: #f3e2e7;
            color: #5d3f4d;
        }

        .sidebar-footer {
            margin-top: auto;
            border-top: 1px solid #e8dddd;
            padding-top: 1rem;
        }

        .main {
            padding: 1rem 1.2rem 1.4rem;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.1rem;
        }

        .search-box {
            width: min(360px, 100%);
            background: #f7efef;
            border: 1px solid #eee3e1;
            display: flex;
            align-items: center;
            padding: 0.6rem 0.8rem;
            color: #9a888e;
        }

        .search-box input {
            border: 0;
            background: transparent;
            outline: none;
            width: 100%;
            font-size: 0.9rem;
            color: #5b4d53;
            margin-left: 0.6rem;
        }

        .top-title {
            font-family: 'Playfair Display', serif;
            color: #57464c;
            font-size: 2rem;
            margin: 0;
        }

        .icon-btn {
            width: 38px;
            height: 38px;
            border: 1px solid #e4d7d8;
            background: #fff;
            color: #836872;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .action-btn {
            background: var(--accent);
            color: #fff;
            border: 0;
            border-radius: 0;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.7rem 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .action-btn:hover {
            background: var(--accent-dark);
            color: #fff;
        }

        .muted-text {
            color: #8f7d81;
        }

        .panel,
        .review-card,
        .staff-card,
        .register-card,
        .summary-card {
            background: #fff;
            border: 1px solid #efe4e2;
            box-shadow: 0 12px 22px rgba(83, 58, 64, 0.04);
        }

        .panel {
            padding: 1rem 1.05rem;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            color: #3f3338;
            font-size: 2.1rem;
            margin: 0;
        }

        .sub-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.65rem;
            margin: 0;
            color: #3e3136;
        }

        .summary-row {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.75rem;
        }

        .summary-card {
            padding: 0.8rem 0.9rem;
        }

        .summary-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #8a767c;
        }

        .summary-value {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: var(--accent);
            line-height: 1.1;
            margin-top: 0.4rem;
        }

        .toolbar-tabs .nav-link {
            border-radius: 0;
            border: 1px solid #e4d7d8;
            color: #6c5a60;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.72rem;
            font-weight: 700;
            background: #fff;
        }

        .toolbar-tabs .nav-link.active {
            background: #f3e2e7;
            color: #5d3f4d;
            border-color: #dfc8d1;
        }

        .staff-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.8rem;
        }

        .staff-card {
            padding: 0.8rem;
            min-height: 164px;
        }

        .staff-head {
            display: flex;
            align-items: flex-start;
            gap: 0.8rem;
            margin-bottom: 0.6rem;
        }

        .avatar {
            width: 46px;
            height: 46px;
            border-radius: 8px;
            background: linear-gradient(135deg, #d8cbd0, #c6b4bc);
            color: #fff;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .staff-name {
            font-family: 'Playfair Display', serif;
            color: #4b3a40;
            font-size: 1.2rem;
            margin: 0;
            line-height: 1.1;
        }

        .staff-role {
            color: #87767c;
            font-size: 0.84rem;
            margin: 0.12rem 0 0.35rem;
        }

        .staff-email {
            color: #9b8a8f;
            font-size: 0.74rem;
        }

        .top-performer {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            background: #f6e8f1;
            color: #875f75;
            border: 1px solid #e5cfdd;
            font-size: 0.68rem;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            font-weight: 700;
            padding: 0.2rem 0.45rem;
            margin-bottom: 0.3rem;
        }

        .rating-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 0.75rem;
            border-top: 1px solid #f1e7e5;
            padding-top: 0.55rem;
            font-size: 0.8rem;
            color: #806e74;
        }

        .star {
            color: #8b6472;
            font-weight: 700;
            margin-right: 0.15rem;
        }

        .register-card {
            padding: 0.95rem;
        }

        .form-label {
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.7rem;
            color: #816f75;
            font-weight: 700;
        }

        .form-control,
        .form-select {
            border-radius: 0;
            border: 1px solid #e3d5d7;
            background: #fff;
        }

        .review-list {
            display: grid;
            gap: 0.85rem;
        }

        .review-card {
            padding: 0.9rem;
        }

        .quote {
            font-family: 'Playfair Display', serif;
            color: #3f3338;
            font-size: 1.28rem;
            line-height: 1.2;
            margin: 0 0 0.45rem;
        }

        .review-stars {
            color: #875f74;
            letter-spacing: 2px;
            font-size: 0.92rem;
        }

        .review-note {
            color: #78696f;
            font-size: 0.9rem;
            margin-top: 0.45rem;
        }

        .review-foot {
            margin-top: 0.8rem;
            padding-top: 0.65rem;
            border-top: 1px solid #f0e6e4;
            display: flex;
            justify-content: space-between;
            gap: 0.5rem;
            font-size: 0.75rem;
            color: #8f7d81;
        }

        @media (max-width: 1199.98px) {
            .shell {
                grid-template-columns: 1fr;
            }

            .sidebar {
                border-right: 0;
                border-bottom: 1px solid var(--line);
            }
        }

        @media (max-width: 767.98px) {
            .staff-grid {
                grid-template-columns: 1fr;
            }

            .summary-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <div>
                <a class="brand" href="index.php?page=admin">Merish</a>
                <div class="brand-sub">Management Portal</div>
            </div>

            <a class="new-booking" href="index.php?page=admin&action=manageReservations">+ New Appointment</a>

            <nav class="nav flex-column side-nav gap-1">
                <a class="nav-link" href="index.php?page=admin">Dashboard</a>
                <a class="nav-link" href="index.php?page=admin&action=manageReservations">Appointments</a>
                <a class="nav-link" href="index.php?page=admin&action=manageCafeOrders">Cafe Orders</a>
                <a class="nav-link" href="index.php?page=admin&action=manageMenus">Inventory</a>
                <a class="nav-link active" href="index.php?page=admin&action=manageStaff">Staff Management</a>
                <a class="nav-link" href="index.php?page=admin&action=reports">Analytics</a>
            </nav>

            <div class="sidebar-footer d-grid gap-1">
                <a class="nav-link" href="<?= LOGOUT_URL ?>">Logout</a>
            </div>
        </aside>

        <main class="main">
            <div class="topbar">
                <h1 class="top-title">Admin Overview</h1>
                <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                    <div class="search-box rounded-0">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" id="staff-search" placeholder="Search staff or reviews...">
                    </div>
                    <button class="icon-btn" type="button" aria-label="Refresh" onclick="location.reload()" title="Refresh page">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                    </button>
                    <a class="action-btn" href="index.php?page=admin&action=reports">Export Report</a>
                </div>
            </div>

            <?php if ($flashSuccess || $flashError): ?>
                <div class="mb-3">
                    <?php if ($flashSuccess): ?>
                        <div class="alert alert-success border-0 rounded-0 mb-2"><?php echo $escape($flashSuccess); ?></div>
                    <?php endif; ?>
                    <?php if ($flashError): ?>
                        <div class="alert alert-danger border-0 rounded-0 mb-2"><?php echo $escape($flashError); ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="row g-3 mb-3">
                <div class="col-12 col-lg-8">
                    <div class="summary-row">
                        <div class="summary-card">
                            <div class="summary-label">Total Staff</div>
                            <div class="summary-value"><?php echo number_format($totalStaff); ?></div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-label">Beautician</div>
                            <div class="summary-value"><?php echo number_format((int) ($roleSummary['Beautician'] ?? 0)); ?></div>
                        </div>
                        <div class="summary-card">
                            <div class="summary-label">Barista + Reception</div>
                            <div class="summary-value"><?php echo number_format((int) (($roleSummary['Barista'] ?? 0) + ($roleSummary['Receptionist'] ?? 0))); ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <ul class="nav toolbar-tabs justify-content-lg-end gap-2">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $activeTab === 'staff' ? 'active' : ''; ?>" href="index.php?page=admin&action=manageStaff&tab=staff">Staff Tab</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $activeTab === 'review' ? 'active' : ''; ?>" href="index.php?page=admin&action=manageStaff&tab=review">Review Tab</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="row g-3 align-items-start">
                <div class="col-12 col-xl-7">
                    <div class="panel mb-3">
                        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                            <div>
                                <h2 class="section-title mb-1">Staff Directory</h2>
                                <p class="muted-text mb-0">Manage employee accounts, roles, and schedules.</p>
                            </div>
                            <a href="#register-staff" class="action-btn">Register New Staff</a>
                        </div>

                        <div class="staff-grid" id="staff-grid">
                            <?php if (empty($staff)): ?>
                                <div class="staff-card d-flex align-items-center justify-content-center text-muted">Belum ada data staff.</div>
                            <?php else: ?>
                                <?php foreach ($staff as $person): ?>
                                    <article class="staff-card">
                                        <div class="staff-head">
                                            <div class="avatar"><?php echo strtoupper(substr((string) ($person['NAME'] ?? 'S'), 0, 1)); ?></div>
                                            <div class="flex-grow-1">
                                                <?php if ($topPerformer && (int) $topPerformer['user_id'] === (int) $person['user_id']): ?>
                                                    <div class="top-performer"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="margin-right:3px;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg> Top Performer</div>
                                                <?php endif; ?>
                                                <h3 class="staff-name"><?php echo $escape($person['NAME']); ?></h3>
                                                <p class="staff-role"><?php echo $escape($roleLabel((string) $person['ROLE'])); ?></p>
                                                <div class="staff-email"><?php echo $escape($person['email']); ?></div>
                                            </div>
                                        </div>

                                        <div class="rating-row">
                                            <div><svg width="13" height="13" viewBox="0 0 24 24" fill="#c9a227" stroke="#c9a227" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="margin-right:3px;vertical-align:middle;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg><?php echo $formatRating($person['avg_rating']); ?></div>
                                            <div><?php echo number_format((int) $person['total_reviews']); ?> reviews</div>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div id="register-staff" class="register-card">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                            <h3 class="sub-title">Register Staff</h3>
                            <span class="muted-text small">Create User</span>
                        </div>

                        <form action="index.php?page=admin&action=registerStaff" method="post" class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Nama staff" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="staff@merish.com" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Minimal 5 karakter" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="Receptionist">Receptionist</option>
                                    <option value="Barista">Barista</option>
                                    <option value="Beautician">Beautician</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="action-btn w-100">Register Staff</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-12 col-xl-5">
                    <div class="panel">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                            <div>
                                <h2 class="section-title mb-1">Recent Testimonials</h2>
                                <p class="muted-text mb-0">Live feed of client feedback.</p>
                            </div>
                            <div class="text-end small muted-text">Sort: Newest</div>
                        </div>

                        <div class="review-list">
                            <?php if (empty($reviews)): ?>
                                <div class="review-card text-muted">Belum ada ulasan dari pelanggan.</div>
                            <?php else: ?>
                                <?php foreach ($reviews as $review): ?>
                                    <article class="review-card">
                                        <h3 class="quote">"<?php echo $escape($review['subject_name']); ?>"</h3>
                                        <div class="review-stars"><?php echo str_repeat('â˜…', max(1, (int) $review['rating'])); ?></div>
                                        <p class="review-note"><?php echo $escape($review['review_comment'] ?: 'Customer tidak menulis komentar tambahan.'); ?></p>
                                        <div class="review-foot">
                                            <span>Service by <?php echo $escape($review['staff_name']); ?></span>
                                            <span>Client: <?php echo $escape($review['customer_name']); ?></span>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

<script>
(function() {
    const input = document.getElementById('staff-search');
    if (!input) return;
    input.addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        // Filter staff cards
        document.querySelectorAll('#staff-grid article').forEach(function(card) {
            card.style.display = card.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
        // Filter review table rows
        document.querySelectorAll('#review-tbody tr').forEach(function(row) {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
})();
</script>
</body>
</html>



