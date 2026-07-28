<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../includes/csrf.php';

$error = $_SESSION['contact_error'] ?? '';
$old = $_SESSION['contact_old'] ?? [];

unset($_SESSION['contact_error'], $_SESSION['contact_old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Contact | SilentShield</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<main class="mobile-screen">
    <section class="mobile-content add-contact-content">
        <header class="page-heading add-contact-heading">
            <a class="back-button" href="index.php">‹</a>

            <div>
                <h1>Add Contact</h1>
                <p>Add someone you trust</p>
            </div>
        </header>

        <div class="contact-photo">
            <span>▣</span>
            <b>＋</b>
        </div>

        <p class="photo-label">Add profile photo</p>

        <?php if ($error): ?>
            <div class="message message-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form
            class="mobile-form add-contact-form"
            action="save_contact.php"
            method="POST"
        >
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(csrf_token()) ?>"
            >

            <div class="form-group">
                <label for="contact_name">FULL NAME</label>

                <div class="input-box">
                    <span class="field-icon icon-user"></span>

                    <input
                        id="contact_name"
                        type="text"
                        name="contact_name"
                        placeholder="Contact’s full name"
                        value="<?= htmlspecialchars($old['contact_name'] ?? '') ?>"
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
                <label for="email">EMAIL ADDRESS</label>

                <div class="input-box">
                    <span class="field-icon icon-email"></span>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        placeholder="Optional email"
                        value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="relationship">RELATIONSHIP</label>

                <div class="input-box">
                    <span class="field-icon icon-user"></span>

                    <input
                        id="relationship"
                        type="text"
                        name="relationship"
                        placeholder="e.g. Parent, Friend, Partner"
                        value="<?= htmlspecialchars($old['relationship'] ?? '') ?>"
                        required
                    >
                </div>
            </div>

            <h2 class="permission-heading">ALERT PERMISSIONS</h2>

            <label class="permission-row">
                <span>Receive emergency SMS</span>

                <span class="switch">
                    <input
                        type="checkbox"
                        name="receive_sms"
                        value="1"
                        checked
                    >
                    <i></i>
                </span>
            </label>

            <label class="permission-row">
                <span>Receive live location link</span>

                <span class="switch">
                    <input
                        type="checkbox"
                        name="receive_location"
                        value="1"
                        checked
                    >
                    <i></i>
                </span>
            </label>

            <label class="permission-row">
                <span>Receive evidence update</span>

                <span class="switch">
                    <input
                        type="checkbox"
                        name="receive_evidence"
                        value="1"
                    >
                    <i></i>
                </span>
            </label>

            <button class="primary-button save-contact-button" type="submit">
                Save Contact
            </button>
        </form>
    </section>
</main>

</body>
</html>