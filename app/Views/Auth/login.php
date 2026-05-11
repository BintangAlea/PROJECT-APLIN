<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Merish</title>
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
                    <h1 class="h3 fw-bold mb-2">Welcome Back</h1>
                    <p class="text-muted small">Sign in to your account</p>
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

                <!-- Login Form -->
                <form method="POST" action="<?php echo url('auth/login'); ?>">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input 
                            type="email" 
                            class="form-control" 
                            id="email" 
                            name="email" 
                            required
                            autofocus
                            placeholder="you@example.com"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                        >
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password" 
                            required
                            placeholder="Enter your password"
                        >
                    </div>

                    <button type="submit" class="btn btn-merish w-100 py-2 fw-bold mb-3">
                        Sign In
                    </button>
                </form>

                <!-- Divider -->
                <hr class="my-4">

                <!-- Footer Links -->
                <div class="text-center">
                    <p class="text-muted small mb-2">Don't have an account?</p>
                    <a href="<?php echo url('auth/register'); ?>" class="btn btn-link text-merish text-decoration-none fw-bold">
                        Create an account
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
