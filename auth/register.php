<?php
session_start();

require_once '../includes/csrf.php';

$error = $_SESSION['register_error'] ?? '';
$old = $_SESSION['register_old'] ?? [];

unset($_SESSION['register_error'], $_SESSION['register_old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | SilentShield</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<main class="mobile-screen">
    <section class="mobile-content register-content">
        <header class="page-heading">
            <a class="back-button" href="../welcome.php">‹</a>

            <div>
                <h1>Create your account</h1>
                <p>Set up your safety profile</p>
            </div>
        </header>

        <?php if ($error): ?>
            <div class="message message-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form
            class="mobile-form register-form"
            action="register_process.php"
            method="POST"
        >
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(csrf_token()) ?>"
            >

            <div class="form-group">
                <label for="full_name">FULL NAME</label>

                <div class="input-box">
                    <span class="field-icon icon-user"></span>

                    <input
                        id="full_name"
                        type="text"
                        name="full_name"
                        placeholder="Enter your full name"
                        value="<?= htmlspecialchars($old['full_name'] ?? '') ?>"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="email">EMAIL ADDRESS</label>

                <div class="input-box">
                    <span class="field-icon icon-email"></span>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        placeholder="name@example.com"
                        value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="phone">PHONE NUMBER</label>

                <div class="input-box">
                    <span class="field-icon icon-phone"></span>

                    <input
                        id="phone"
                        type="tel"
                        name="phone"
                        placeholder="+880 1XXXXXXXXX"
                        value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="password">PASSWORD</label>

                <div class="input-box">
                    <span class="field-icon icon-lock"></span>

                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Minimum 8 characters"
                        minlength="8"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">CONFIRM PASSWORD</label>

                <div class="input-box">
                    <span class="field-icon icon-lock"></span>

                    <input
                        id="confirm_password"
                        type="password"
                        name="confirm_password"
                        placeholder="Re-enter password"
                        minlength="8"
                        required
                    >
                </div>
            </div>

            <label class="terms-check">
                <input type="checkbox" name="terms" value="1" required>
                <span>I agree to the Terms and Privacy Policy</span>
            </label>

            <button class="primary-button" type="submit">
                Create Account
            </button>
        </form>

        <div class="separator">or continue with</div>

        <button class="outline-button" type="button">
            Continue with Google
        </button>

        <p class="auth-footer">
            Already have an account?
            <a href="login.php">Log in</a>
        </p>
    </section>
</main>

</body>
</html>