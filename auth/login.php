<?php
session_start();

require_once '../includes/csrf.php';

$error = $_SESSION['login_error'] ?? '';
$identifier = $_SESSION['login_identifier'] ?? '';

unset($_SESSION['login_error'], $_SESSION['login_identifier']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In | SilentShield</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<main class="mobile-screen">
    <section class="mobile-content login-content">
        <header class="login-header">
            <h1>Welcome back</h1>
            <p>Log in to continue</p>
        </header>

        <div class="shield-badge">
            <div class="shield"></div>
        </div>

        <?php if ($error): ?>
            <div class="message message-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form
            class="mobile-form login-form"
            action="login_process.php"
            method="POST"
        >
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(csrf_token()) ?>"
            >

            <div class="form-group">
                <label for="identifier">EMAIL OR PHONE</label>

                <div class="input-box">
                    <span class="field-icon icon-email"></span>

                    <input
                        id="identifier"
                        type="text"
                        name="identifier"
                        placeholder="Enter email or phone"
                        value="<?= htmlspecialchars($identifier) ?>"
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
                        placeholder="Enter your password"
                        required
                    >
                </div>
            </div>

            <a class="forgot-link" href="forgot_password.php">
                Forgot password?
            </a>

            <button class="primary-button" type="submit">
                Log In
            </button>
        </form>

        <div class="separator login-separator">or</div>

        <button class="outline-button" type="button">
            Continue with Google
        </button>

        <section class="privacy-box">
            <span class="mini-shield">♢</span>

            <div>
                <h2>Your privacy matters</h2>

                <p>
                    Your data is encrypted and protected.<br>
                    We never share it without permission.
                </p>
            </div>
        </section>

        <p class="auth-footer login-footer">
            New to SilentShield?
            <a href="register.php">Create an account</a>
        </p>
    </section>
</main>

</body>
</html>