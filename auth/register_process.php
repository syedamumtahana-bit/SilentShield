<?php
session_start();

require_once '../config/db.php';
require_once '../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    $_SESSION['register_error'] = 'Invalid request. Please try again.';
    header('Location: register.php');
    exit;
}

$fullName = trim($_POST['full_name'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$phone = trim($_POST['phone'] ?? '');
$dateOfBirth = trim($_POST['date_of_birth'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

$_SESSION['register_old'] = [
    'full_name' => $fullName,
    'email' => $email,
    'phone' => $phone,
    'date_of_birth' => $dateOfBirth
];

if ($fullName === '' || $email === '' || $phone === '' || $password === '') {
    $_SESSION['register_error'] = 'Please complete all required fields.';
    header('Location: register.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['register_error'] = 'Please enter a valid email address.';
    header('Location: register.php');
    exit;
}

if (strlen($password) < 8) {
    $_SESSION['register_error'] = 'Password must contain at least 8 characters.';
    header('Location: register.php');
    exit;
}

if ($password !== $confirmPassword) {
    $_SESSION['register_error'] = 'The passwords do not match.';
    header('Location: register.php');
    exit;
}

try {
    $statement = $pdo->prepare(
        'SELECT user_id FROM users WHERE email = ? LIMIT 1'
    );
    $statement->execute([$email]);

    if ($statement->fetch()) {
        $_SESSION['register_error'] = 'An account already uses this email.';
        header('Location: register.php');
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $dateValue = $dateOfBirth !== '' ? $dateOfBirth : null;

    $statement = $pdo->prepare(
        'INSERT INTO users
        (full_name, email, password, phone, date_of_birth, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, NOW(), NOW())'
    );

    $statement->execute([
        $fullName,
        $email,
        $passwordHash,
        $phone,
        $dateValue
    ]);

    session_regenerate_id(true);

    $_SESSION['user_id'] = (int) $pdo->lastInsertId();
    $_SESSION['full_name'] = $fullName;
    $_SESSION['email'] = $email;

    unset($_SESSION['register_old']);

    header('Location: ../dashboard/dashboard.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['register_error'] = 'Registration failed. Please try again.';
    header('Location: register.php');
    exit;
}