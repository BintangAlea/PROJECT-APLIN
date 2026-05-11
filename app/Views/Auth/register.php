<?php
/**
 * app/Views/Auth/register.php
 * Registration page with Bootstrap styling
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Merish</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/SIB/PROJECT-APLIN/assets/css/style.css">
</head>
<body class="bg-light">
    <div class="min-vh-100 d-flex align-items-center justify-content-center py-4">
        <div class="card shadow-lg" style="width: 100%; max-width: 400px;">
            <div class="card-body p-5">
                <!-- Header -->
                <div class="text-center mb-4">
                    <div class="text-merish fw-bold mb-2" style="font-size: 32px;">Merish</div>
                    <h1 class="h3 fw-bold mb-2">Create Account</h1>
                    <p class="text-muted small">Join our community today</p>
                </div>

                <!-- Error Message -->
                <?php if (isset($error) && $error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Success Message -->
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_GET['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Register Form -->
                <form method="POST" action="<?php echo url('auth/register'); ?>">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="full_name" 
                            name="full_name" 
                            required
                            placeholder="Your full name"
                            value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>"
                        >
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input 
                            type="email" 
                            class="form-control" 
                            id="email" 
                            name="email" 
                            required
                            placeholder="you@example.com"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                        >
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input 
                            type="tel" 
                            class="form-control" 
                            id="phone" 
                            name="phone"
                            placeholder="+62 812 3456 7890"
                            value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                        >
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">-- Select Role --</option>
                            <option value="customer" selected>Customer</option>
                            <option value="barista">Barista</option>
                            <option value="beautician">Beautician</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password" 
                            required
                            placeholder="Min. 8 characters"
                        >
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="confirm_password" 
                            name="confirm_password" 
                            required
                            placeholder="Re-enter your password"
                        >
                    </div>

                    <button type="submit" class="btn btn-merish w-100 py-2 fw-bold mb-3">
                        Create Account
                    </button>
                </form>

                <!-- Divider -->
                <hr class="my-4">

                <!-- Footer Links -->
                <div class="text-center">
                    <p class="text-muted small mb-2">Already have an account?</p>
                    <a href="<?php echo url('auth/login'); ?>" class="btn btn-link text-merish text-decoration-none fw-bold">
                        Sign in instead
                    </a>
                    <br>
                    <a href="<?php echo url(''); ?>" class="btn btn-link text-muted text-decoration-none small">
                        ← Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/SIB/PROJECT-APLIN/assets/js/script.js"></script>
</body>
</html>
