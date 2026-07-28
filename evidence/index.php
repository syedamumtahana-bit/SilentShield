<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/db.php';

$userId = (int) $_SESSION['user_id'];

$statement = $pdo->prepare(
    'SELECT
        evidence_id,
        alert_id,
        file_name,
        file_type,
        uploaded_at
     FROM evidence
     WHERE user_id = ?
     ORDER BY uploaded_at DESC, evidence_id DESC'
);

$statement->execute([$userId]);
$evidenceFiles = $statement->fetchAll();

$message = $_SESSION['evidence_message'] ?? '';
$error = $_SESSION['evidence_error'] ?? '';

unset($_SESSION['evidence_message'], $_SESSION['evidence_error']);

function evidenceIcon(string $fileType): string
{
    if (str_starts_with($fileType, 'image/')) {
        return '▧';
    }

    if ($fileType === 'application/pdf') {
        return 'PDF';
    }

    if (str_starts_with($fileType, 'video/')) {
        return '▶';
    }

    if (str_starts_with($fileType, 'audio/')) {
        return '♪';
    }

    return '▤';
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

    <title>Evidence | SilentShield</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >
</head>
<body>

<main class="mobile-screen evidence-screen">
    <section class="mobile-content evidence-content">
        <header class="evidence-heading">
            <a
                class="back-button"
                href="../dashboard/dashboard.php"
            >
                ‹
            </a>

            <div>
                <h1>Evidence</h1>

                <p>
                    <?= count($evidenceFiles) ?>
                    <?= count($evidenceFiles) === 1 ? 'secure file' : 'secure files' ?>
                </p>
            </div>

            <a
                class="evidence-add-icon"
                href="upload.php"
                aria-label="Upload evidence"
            >
                ＋
            </a>
        </header>

        <?php if ($message): ?>
            <div class="message message-success">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="message message-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <section class="evidence-privacy-card">
            <span class="evidence-shield">✓</span>

            <div>
                <strong>Your evidence is protected</strong>

                <p>
                    Files are private and available only through your account.
                </p>
            </div>
        </section>

        <?php if (!$evidenceFiles): ?>
            <section class="empty-evidence">
                <span>↥</span>

                <h2>No evidence uploaded</h2>

                <p>
                    Securely save photos, videos, audio recordings,
                    documents, and other important files.
                </p>

                <a class="primary-button" href="upload.php">
                    Upload Evidence
                </a>
            </section>
        <?php else: ?>
            <section class="evidence-list">
                <?php foreach ($evidenceFiles as $file): ?>
                    <article class="evidence-card">
                        <span class="evidence-file-icon">
                            <?= htmlspecialchars(evidenceIcon($file['file_type'])) ?>
                        </span>

                        <div class="evidence-file-details">
                            <h2>
                                <?= htmlspecialchars($file['file_name']) ?>
                            </h2>

                            <p>
                                <?= htmlspecialchars($file['file_type']) ?>
                            </p>

                            <small>
                                <?= date(
                                    'd M Y, h:i A',
                                    strtotime($file['uploaded_at'])
                                ) ?>
                            </small>
                        </div>

                        <a
                            class="evidence-download"
                            href="download.php?id=<?= (int) $file['evidence_id'] ?>"
                        >
                            ↓
                        </a>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>

        <?php if ($evidenceFiles): ?>
            <a class="primary-button evidence-upload-button" href="upload.php">
                ＋ Upload New Evidence
            </a>
        <?php endif; ?>
    </section>

    <nav class="dashboard-navigation">
        <a
            class="dashboard-nav-item"
            href="../dashboard/dashboard.php"
        >
            <span>⌂</span>
            <small>Home</small>
        </a>

        <a
            class="dashboard-nav-item"
            href="../contacts/index.php"
        >
            <span>♧</span>
            <small>Contacts</small>
        </a>

        <a
            class="dashboard-nav-sos"
            href="../alerts/sos.php"
        >
            <span class="navigation-shield">♢</span>
            <small>SOS</small>
        </a>

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
</main>

</body>
</html>