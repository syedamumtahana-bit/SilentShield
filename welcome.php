<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | SilentShield</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<main class="mobile-screen">
    <div class="background-circle welcome-circle-top"></div>
    <div class="background-circle welcome-circle-left"></div>

    <section class="welcome-content">
        <div class="shield-badge">
            <div class="shield"></div>
        </div>

        <h1>Welcome to SilentShield</h1>

        <p class="welcome-description">
            Stay connected. Stay protected.<br>
            Send emergency alerts, share your location,<br>
            and keep trusted contacts close.
        </p>

        <div class="welcome-actions">
            <a class="primary-button" href="auth/register.php">
                Create Account
            </a>

            <a class="outline-button" href="auth/login.php">
                I already have an account
            </a>
        </div>

        <p class="welcome-terms">
            By continuing, you agree to our
            <a href="#">Terms of Service and Privacy Policy</a>
        </p>
    </section>
</main>

</body>
</html>