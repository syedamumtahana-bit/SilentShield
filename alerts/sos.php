<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/db.php';
require_once '../includes/csrf.php';

$userId = (int) $_SESSION['user_id'];

$statement = $pdo->prepare(
    'SELECT COUNT(*)
     FROM emergency_contacts
     WHERE user_id = ?'
);
$statement->execute([$userId]);

$contactCount = (int) $statement->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SOS Activated | SilentShield</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<main class="mobile-screen sos-activated-screen">
    <section class="sos-activated-content">
        <p class="emergency-mode">EMERGENCY MODE</p>

        <div class="emergency-symbol">
            <div class="warning-triangle">!</div>
        </div>

        <h1>SOS Activated</h1>
        <p class="sending-label">Sending alert in</p>

        <strong class="countdown" id="countdown">03</strong>
        <span class="seconds-label">seconds</span>

        <div class="sos-progress">
            <div id="sosProgressBar"></div>
        </div>

        <section class="alerting-box">
            <span>♧</span>

            <div>
                <strong>
                    Alerting <?= $contactCount ?> emergency contacts
                </strong>

                <p>Your live location will be shared.</p>
            </div>
        </section>

        <form action="cancel_sos.php" method="POST">
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(csrf_token()) ?>"
            >

            <button class="cancel-sos-button" type="submit">
                Cancel SOS
            </button>
        </form>

        <p class="safety-reminder">
            Keep your phone with you and move to a safe place.
        </p>
    </section>
</main>

<script>
const csrfToken = <?= json_encode(csrf_token()) ?>;
const countdownElement = document.getElementById('countdown');
const progressBar = document.getElementById('sosProgressBar');

let seconds = 3;
let alertSent = false;

function sendAlert(latitude, longitude) {
    if (alertSent) {
        return;
    }

    alertSent = true;

    fetch('activate_sos.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            csrf_token: csrfToken,
            latitude: latitude,
            longitude: longitude
        })
    })
    .then(function (response) {
        return response.json();
    })
    .then(function (result) {
        if (result.success) {
            countdownElement.textContent = '✓';
            document.querySelector('.sending-label').textContent =
                'Emergency alert sent';
        }
    })
    .catch(function () {
        alertSent = false;
    });
}

function activateSOS() {
    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                sendAlert(
                    position.coords.latitude,
                    position.coords.longitude
                );
            },
            function () {
                sendAlert(null, null);
            },
            {
                enableHighAccuracy: true,
                timeout: 7000
            }
        );
    } else {
        sendAlert(null, null);
    }
}

const countdownTimer = window.setInterval(function () {
    seconds -= 1;

    countdownElement.textContent =
        String(Math.max(seconds, 0)).padStart(2, '0');

    progressBar.style.width =
        ((3 - seconds) / 3 * 100) + '%';

    if (seconds <= 0) {
        window.clearInterval(countdownTimer);
        activateSOS();
    }
}, 1000);
</script>

</body>
</html>