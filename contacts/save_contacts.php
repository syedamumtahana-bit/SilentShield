<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/db.php';
require_once '../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: add.php');
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    $_SESSION['contact_error'] = 'Invalid request. Please try again.';
    header('Location: add.php');
    exit;
}

$contactName = trim($_POST['contact_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$relationship = trim($_POST['relationship'] ?? '');

$receiveSms = isset($_POST['receive_sms']) ? 1 : 0;
$receiveLocation = isset($_POST['receive_location']) ? 1 : 0;
$receiveEvidence = isset($_POST['receive_evidence']) ? 1 : 0;

$_SESSION['contact_old'] = [
    'contact_name' => $contactName,
    'phone' => $phone,
    'email' => $email,
    'relationship' => $relationship
];

if ($contactName === '' || $phone === '' || $relationship === '') {
    $_SESSION['contact_error'] = 'Complete all required fields.';
    header('Location: add.php');
    exit;
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['contact_error'] = 'Enter a valid email address.';
    header('Location: add.php');
    exit;
}

try {
    $statement = $pdo->prepare(
        'INSERT INTO emergency_contacts
        (
            user_id,
            contact_name,
            phone,
            email,
            relationship,
            receive_sms,
            receive_location,
            receive_evidence
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );

    $statement->execute([
        (int) $_SESSION['user_id'],
        $contactName,
        $phone,
        $email !== '' ? $email : null,
        $relationship,
        $receiveSms,
        $receiveLocation,
        $receiveEvidence
    ]);

    unset($_SESSION['contact_old']);

    $_SESSION['contact_message'] = 'Emergency contact added successfully.';

    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    $_SESSION['contact_error'] = 'Unable to save the contact.';
    header('Location: add.php');
    exit;
}