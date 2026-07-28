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
    'SELECT alert_id, alert_time, status
     FROM alerts
     WHERE user_id = ?
     ORDER BY alert_time DESC
     LIMIT 20'
);

$statement->execute([$userId]);
$alerts = $statement->fetchAll();

$error = $_SESSION['evidence_error'] ?? '';
unset($_SESSION['evidence_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Upload Evidence | SilentShield</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >
</head>
<body>

<main class="mobile-screen">
    <section class="mobile-content evidence-upload-content">
        <header class="page-heading evidence-upload-heading">
            <a class="back-button" href="index.php">‹</a>

            <div>
                <h1>Upload Evidence</h1>
                <p>Securely save an important file</p>
            </div>
        </header>

        <?php if ($error): ?>
            <div class="message message-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form
            class="evidence-upload-form"
            action="upload_process.php"
            method="POST"
            enctype="multipart/form-data"
        >
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(csrf_token()) ?>"
            >

            <label class="upload-drop-area" for="evidence_file">
                <span>↥</span>

                <strong>Choose evidence file</strong>

                <p>
                    Photo, video, audio or PDF
                </p>

                <small>Maximum file size: 10 MB</small>

                <input
                    id="evidence_file"
                    type="file"
                    name="evidence_file"
                    accept="image/jpeg,image/png,image/webp,application/pdf,video/mp4,audio/mpeg,audio/wav"
                    required
                >
            </label>

            <p class="selected-file" id="selectedFile">
                No file selected
            </p>

            <div class="form-group">
                <label for="alert_id">RELATED ALERT — OPTIONAL</label>

                <select id="alert_id" name="alert_id">
                    <option value="">Not connected to an alert</option>

                    <?php foreach ($alerts as $alert): ?>
                        <option value="<?= (int) $alert['alert_id'] ?>">
                            Alert #<?= (int) $alert['alert_id'] ?>
                            —
                            <?= date(
                                'd M Y, h:i A',
                                strtotime($alert['alert_time'])
                            ) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <section class="upload-warning">
                <span>!</span>

                <p>
                    <strong>Keep the original file.</strong><br>
                    Do not edit important evidence before uploading it.
                </p>
            </section>

            <button class="primary-button" type="submit">
                Upload Securely
            </button>
        </form>
    </section>
</main>

<script>
const evidenceInput = document.getElementById('evidence_file');
const selectedFile = document.getElementById('selectedFile');

evidenceInput.addEventListener('change', function () {
    if (this.files.length > 0) {
        selectedFile.textContent = this.files[0].name;
    } else {
        selectedFile.textContent = 'No file selected';
    }
});
</script>

</body>
</html>