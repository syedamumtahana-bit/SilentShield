<?php
session_start();

require_once '../config/db.php';
require_once '../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: forgot_password.php');
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    $_SESSION['reset_error'] = 'Invalid request. Please try again.';
    header('Location: forgot_password.php');
    exit;
}

$identifier = trim($_POST['identifier'] ?? '');

if ($identifier === '') {
    $_SESSION['reset_error'] = 'Enter your registered email or phone.';
    header('Location: forgot_password.php');
    exit;
}

try {
    $statement = $pdo->prepare(
    'SELECT user_id
     FROM users
     WHERE email = :email_identifier
        OR phone = :phone_identifier
     LIMIT 1'
);

$statement->execute([
    'email_identifier' => $identifier,
    'phone_identifier' => $identifier
]);

    $user = $statement->fetch();

    if ($user) {
        $_SESSION['reset_user_id'] = (int) $user['user_id'];
        $_SESSION['reset_code'] = (string) random_int(100000, 999999);
    }

    $_SESSION['reset_success'] =
        'If an account matches that information, a verification code has been prepared.';

    header('Location: forgot_password.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['reset_error'] = 'Unable to process your request.';
    header('Location: forgot_password.php');
    exit;
}