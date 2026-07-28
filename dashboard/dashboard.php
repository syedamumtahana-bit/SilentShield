<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/db.php';

date_default_timezone_set('Asia/Dhaka');

$userId = (int) $_SESSION['user_id'];
$fullName = trim($_SESSION['full_name'] ?? 'User');
$firstName = explode(' ', $fullName)[0];
$profileLetter = strtoupper(substr($firstName, 0, 1));

$hour = (int) date('G');

if ($hour < 12) {
    $greeting = 'Good morning';
} elseif ($hour < 17) {
    $greeting = 'Good afternoon';
} else {
    $greeting = 'Good evening';
}

$contactCount = 0;
$alertCount = 0;

try {
    $statement = $pdo->prepare(
        'SELECT COUNT(*)
         FROM emergency_contacts
         WHERE user_id = ?'
    );

    $statement->execute([$userId]);
    $contactCount = (int) $statement->fetchColumn();

    $statement = $pdo->prepare(
        'SELECT COUNT(*)
         FROM alerts
         WHERE user_id = ?'
    );

    $statement->execute([$userId]);
    $alertCount = (int) $statement->fetchColumn();
} catch (PDOException $e) {
    $contactCount = 0;
    $alertCount = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Home | SilentShield</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >
</head>
<body>

<main class="dashboard-app">
    <header class="dashboard-top">
        <div class="dashboard-welcome">
            <p><?= htmlspecialchars($greeting) ?>,</p>
            <h1><?= htmlspecialchars($firstName) ?></h1>
        </div>

        <a
            class="dashboard-avatar"
            href="../profile/index.php"
            aria-label="Open profile"
        >
            <?= htmlspecialchars($profileLetter) ?>
        </a>

        <section class="safety-card">
            <div class="safety-shield">
                <span>✓</span>
            </div>

            <div class="safety-copy">
                <p>Safety status</p>
                <strong>Protected and ready</strong>
            </div>

            <span class="active-badge">ACTIVE</span>
        </section>
    </header>

    <section class="dashboard-main">
        <button
            class="main-sos-button"
            id="mainSosButton"
            type="button"
            aria-label="Press and hold for three seconds to activate SOS"
        >
            <span class="sos-inner">
                <strong>SOS</strong>
                <small>PRESS &amp; HOLD</small>
            </span>
        </button>

        <p class="hold-instruction">
            Hold for 3 seconds to activate emergency alert
        </p>

        <h2 class="quick-heading">QUICK ACTIONS</h2>

        <section class="dashboard-actions">
            <a
                class="dashboard-action-card"
                href="../contacts/index.php"
            >
                <span class="dashboard-action-icon contacts-icon">
                    ♧
                </span>

                <strong>Contacts</strong>

                <small>
                    <?= $contactCount ?>
                    <?= $contactCount === 1 ? 'person' : 'people' ?>
                </small>
            </a>

            <a
                class="dashboard-action-card"
                href="../evidence/index.php"
            >
                <span class="dashboard-action-icon evidence-icon">
                    ↥
                </span>

                <strong>Evidence</strong>
                <small>Upload files</small>
            </a>

            <a
                class="dashboard-action-card"
                href="../history/index.php"
            >
                <span class="dashboard-action-icon history-icon">
                    ◴
                </span>

                <strong>Alert history</strong>

                <small>
                    <?= $alertCount ?>
                    <?= $alertCount === 1 ? 'alert' : 'alerts' ?>
                </small>
            </a>

            <a
                class="dashboard-action-card"
                href="tel:999"
            >
                <span class="dashboard-action-icon help-icon">
                    ?
                </span>

                <strong>Help center</strong>
                <small>24/7 support</small>
            </a>
        </section>
    </section>

    <nav class="dashboard-navigation">
        <a
            class="dashboard-nav-item active"
            href="dashboard.php"
        >
            <span class="home-nav-icon">⌂</span>
            <small>Home</small>
        </a>

        <a
            class="dashboard-nav-item"
            href="../contacts/index.php"
        >
            <span>♧</span>
            <small>Contacts</small>
        </a>

        <button
            class="dashboard-nav-sos"
            id="navigationSosButton"
            type="button"
        >
            <span class="navigation-shield">♢</span>
            <small>SOS</small>
        </button>

        <a
            class="dashboard-nav-item"
            href="../history/index.php"
        >
            <span>◴</span>
            <small>History</small>
        </a>

        <a
            class="dashboard-nav-item"
            href="../profile/index.php"
        >
            <span>♙</span>
            <small>Profile</small>
        </a>
    </nav>

    <div class="hold-overlay" id="holdOverlay">
        <div>
            <strong id="holdCount">3</strong>
            <p>Keep holding to activate SOS</p>
        </div>
    </div>
</main>

<script>
const mainSosButton = document.getElementById('mainSosButton');
const navigationSosButton = document.getElementById('navigationSosButton');
const holdOverlay = document.getElementById('holdOverlay');
const holdCount = document.getElementById('holdCount');

let holdTimeout = null;
let countdownInterval = null;
let remainingSeconds = 3;
let sosActivated = false;

function beginHold(event) {
    if (event) {
        event.preventDefault();
    }

    if (holdTimeout || sosActivated) {
        return;
    }

    remainingSeconds = 3;
    holdCount.textContent = remainingSeconds;

    mainSosButton.classList.add('holding');
    holdOverlay.classList.add('visible');

    countdownInterval = window.setInterval(function () {
        remainingSeconds--;

        if (remainingSeconds > 0) {
            holdCount.textContent = remainingSeconds;
        }
    }, 1000);

    holdTimeout = window.setTimeout(function () {
        sosActivated = true;
        stopTimers();

        window.location.href = '../alerts/sos.php';
    }, 3000);
}

function stopTimers() {
    if (holdTimeout) {
        window.clearTimeout(holdTimeout);
        holdTimeout = null;
    }

    if (countdownInterval) {
        window.clearInterval(countdownInterval);
        countdownInterval = null;
    }
}

function cancelHold() {
    if (sosActivated) {
        return;
    }

    stopTimers();

    mainSosButton.classList.remove('holding');
    holdOverlay.classList.remove('visible');
}

mainSosButton.addEventListener('pointerdown', beginHold);
mainSosButton.addEventListener('pointerup', cancelHold);
mainSosButton.addEventListener('pointerleave', cancelHold);
mainSosButton.addEventListener('pointercancel', cancelHold);
mainSosButton.addEventListener('contextmenu', function (event) {
    event.preventDefault();
});

navigationSosButton.addEventListener('click', function () {
    window.location.href = '../alerts/sos.php';
});
</script>

</body>
</html>