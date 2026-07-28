<?php
session_start();

require_once '../config/db.php';
require_once '../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    $_SESSION['login_error'] = 'Invalid request. Please try again.';
    header('Location: login.php');
    exit;
}

$identifier = trim($_POST['identifier'] ?? '');
$password = $_POST['password'] ?? '';

$_SESSION['login_identifier'] = $identifier;

if ($identifier === '' || $password === '') {
    $_SESSION['login_error'] = 'Enter your email or phone and password.';
    header('Location: login.php');
    exit;
}

try {
    $statement = $pdo->prepare(
    'SELECT user_id, full_name, email, phone, password
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

    if (!$user || !password_verify($password, $user['password'])) {
        $_SESSION['login_error'] = 'Incorrect login information.';
        header('Location: login.php');
        exit;
    }

    session_regenerate_id(true);

    $_SESSION['user_id'] = (int) $user['user_id'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['phone'] = $user['phone'];

    unset($_SESSION['login_identifier']);

    header('Location: ../dashboard/dashboard.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['login_error'] = 'Unable to log in right now.';
    header('Location: login.php');
    exit;
}