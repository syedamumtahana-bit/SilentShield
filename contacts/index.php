<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/db.php';

$userId = (int) $_SESSION['user_id'];

$statement = $pdo->prepare(
    'SELECT contact_id, contact_name, phone, email, relationship
     FROM emergency_contacts
     WHERE user_id = ?
     ORDER BY contact_id DESC'
);

$statement->execute([$userId]);
$contacts = $statement->fetchAll();

$message = $_SESSION['contact_message'] ?? '';
$error = $_SESSION['contact_error'] ?? '';

unset($_SESSION['contact_message'], $_SESSION['contact_error']);

$colors = ['pink', 'purple', 'green'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Contacts | SilentShield</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<main class="mobile-screen contacts-screen">
    <section class="mobile-content contacts-content">
        <header class="contacts-heading">
            <a class="back-button" href="../dashboard/dashboard.php">‹</a>

            <div>
                <h1>Emergency Contacts</h1>
                <p><?= count($contacts) ?> trusted contacts</p>
            </div>

            <a class="add-icon" href="add.php">＋</a>
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

        <section class="contact-information">
            <span>♢</span>

            <div>
                <strong>These people receive your alerts</strong>
                <p>Keep their information up to date.</p>
            </div>
        </section>

        <section class="contact-list">
            <?php if (!$contacts): ?>
                <div class="empty-contact-card">
                    <h2>No emergency contacts yet</h2>
                    <p>Add someone you trust to receive your alerts.</p>
                </div>
            <?php endif; ?>

            <?php foreach ($contacts as $index => $contact): ?>
                <?php
                $words = preg_split('/\s+/', trim($contact['contact_name']));
                $initials = '';

                foreach (array_slice($words, 0, 2) as $word) {
                    $initials .= strtoupper(substr($word, 0, 1));
                }

                $color = $colors[$index % count($colors)];
                ?>

                <article class="contact-card">
                    <span class="contact-avatar <?= $color ?>">
                        <?= htmlspecialchars($initials) ?>
                    </span>

                    <div class="contact-details">
                        <h2><?= htmlspecialchars($contact['contact_name']) ?></h2>

                        <p>
                            <?= htmlspecialchars($contact['relationship']) ?>
                        </p>

                        <a href="tel:<?= htmlspecialchars($contact['phone']) ?>">
                            ☎ <?= htmlspecialchars($contact['phone']) ?>
                        </a>
                    </div>

                    <a
                        class="edit-contact"
                        href="edit.php?id=<?= (int) $contact['contact_id'] ?>"
                        aria-label="Edit contact"
                    >
                        ✎
                    </a>

                    <span class="contact-arrow">›</span>
                </article>
            <?php endforeach; ?>
        </section>

        <a class="primary-button add-contact-button" href="add.php">
            ＋ Add New Contact
        </a>

        <section class="contact-recommendation">
            <span>⚠</span>

            <div>
                <strong>Recommended: add at least 2 contacts</strong>
                <p>Choose people who can respond quickly.</p>
            </div>
        </section>
    </section>

    <nav class="bottom-navigation contacts-navigation">
        <a href="../dashboard/dashboard.php">
            <span>⌂</span>
            <small>Home</small>
        </a>

        <a class="active" href="index.php">
            <span>♧</span>
            <small>Contacts</small>
        </a>

        <a href="../alerts/sos.php">
            <span>♢</span>
            <small>SOS</small>
        </a>

        <a href="../history/index.php">
            <span>◴</span>
            <small>History</small>
        </a>

        <a href="../profile/index.php">
            <span>♙</span>
            <small>Profile</small>
        </a>
    </nav>
</main>

</body>
</html>