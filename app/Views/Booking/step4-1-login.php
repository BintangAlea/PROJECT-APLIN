<?php
/**
 * Booking Step 4.1: Login / Register
 */
?>
<?php include __DIR__ . '/../Layout/base.php'; ?>

<div class="booking-container">
    <div class="booking-header">
        <h1><?= htmlspecialchars($title ?? 'Login / Daftar') ?></h1>
        <p>Buat akun atau login untuk melanjutkan pemesanan</p>
    </div>

    <div class="auth-tabs">
        <div class="tab-buttons">
            <button type="button" class="tab-btn active" data-tab="login">Login</button>
            <button type="button" class="tab-btn" data-tab="register">Daftar</button>
        </div>

        <!-- Login Form -->
        <div id="login" class="tab-content active">
            <form method="POST" action="/index.php?page=booking&step=4.1">
                <input type="hidden" name="action" value="login">

                <div class="form-group">
                    <label for="login_email">Email</label>
                    <input type="email" id="login_email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="login_password">Password</label>
                    <input type="password" id="login_password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-full">Masuk</button>
            </form>
        </div>

        <!-- Register Form -->
        <div id="register" class="tab-content">
            <form method="POST" action="/index.php?page=booking&step=4.1">
                <input type="hidden" name="action" value="register">

                <div class="form-group">
                    <label for="register_name">Nama Lengkap</label>
                    <input type="text" id="register_name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="register_email">Email</label>
                    <input type="email" id="register_email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="register_phone">No. Telepon</label>
                    <input type="tel" id="register_phone" name="phone">
                </div>

                <div class="form-group">
                    <label for="register_password">Password</label>
                    <input type="password" id="register_password" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-full">Daftar</button>
            </form>
        </div>
    </div>

    <div class="booking-actions">
        <a href="/index.php?page=booking&step=4" class="btn btn-secondary">← Kembali</a>
    </div>
</div>

<script>
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const tabName = this.getAttribute('data-tab');
        
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });
        
        // Remove active from all buttons
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('active');
        });
        
        // Show selected tab
        document.getElementById(tabName).classList.add('active');
        this.classList.add('active');
    });
});
</script>

<style>
.auth-tabs {
    background: white;
    border-radius: 8px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.tab-buttons {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    border-bottom: 2px solid #e0e0e0;
}

.tab-btn {
    padding: 0.75rem 1.5rem;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1rem;
    color: #666;
    transition: all 0.3s;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
}

.tab-btn.active {
    color: #d4a574;
    border-bottom-color: #d4a574;
}

.tab-btn:hover {
    color: #d4a574;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: #333;
}

.form-group input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form-group input:focus {
    outline: none;
    border-color: #d4a574;
    box-shadow: 0 0 0 2px rgba(212, 165, 116, 0.1);
}

.btn-full {
    width: 100%;
}
</style>
