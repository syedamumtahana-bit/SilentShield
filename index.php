<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SilentShield</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<main class="mobile-screen splash-screen">
    <div class="background-circle splash-circle-top"></div>
    <div class="background-circle splash-circle-bottom"></div>

    <section class="splash-center">
        <div class="shield-badge">
            <div class="shield"></div>
        </div>

        <h1>SilentShield</h1>
        <p>Your safety, always within reach.</p>

        <div class="loading-track">
            <div class="loading-progress"></div>
        </div>
    </section>

    <p class="splash-footer">SECURE • SIMPLE • READY</p>
</main>

<script>
    window.setTimeout(function () {
        window.location.href = "welcome.php";
    }, 3000);
</script>

</body>
</html>