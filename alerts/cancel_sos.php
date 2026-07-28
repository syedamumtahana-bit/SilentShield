<?php
session_start();

require_once '../includes/csrf.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST'
    || !verify_csrf_token($_POST['csrf_token'] ?? null)
) {
    header('Location: sos.php');
    exit;
}

header('Location: ../dashboard/dashboard.php');
exit;