<?php
session_start();

require_once '../includes/csrf.php';

$error = $_SESSION['reset_error'] ?? '';
$success = $_SESSION['reset_success'] ?? '';

unset($_SESSION['reset_error'], $_SESSION['reset_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | SilentShield</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<main class="mobile-screen">
    <section class="mobile-content forgot-content">
        <header class="page-heading">
            <a class="back-button" href="login.php">‹</a>

            <div>
                <h1>Reset password</h1>
                <p>We’ll help you regain access</p>
            </div>
        </header>

        <div class="forgot-lock">
            <div class="large-lock"></div>
        </div>

        <h2 class="forgot-title">Forgot your password?</h2>

        <p class="forgot-description">
            Enter your registered email or phone number.<br>
            We’ll send you a verification code.
        </p>

        <?php if ($error): ?>
            <div class="message message-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="message message-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form
            class="mobile-form forgot-form"
            action="reset_request.php"
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
                        placeholder="name@example.com"
                        required
                    >
                </div>
            </div>

            <button class="primary-button" type="submit">
                Send Verification Code
            </button>
        </form>
    </section>
</main>

</body>
</html>