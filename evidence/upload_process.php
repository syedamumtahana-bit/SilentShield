<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/db.php';
require_once '../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: upload.php');
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    $_SESSION['evidence_error'] = 'Invalid request. Please try again.';
    header('Location: upload.php');
    exit;
}

if (
    !isset($_FILES['evidence_file'])
    || $_FILES['evidence_file']['error'] !== UPLOAD_ERR_OK
) {
    $_SESSION['evidence_error'] = 'Select a valid file to upload.';
    header('Location: upload.php');
    exit;
}

$file = $_FILES['evidence_file'];

$maximumSize = 10 * 1024 * 1024;

if ($file['size'] > $maximumSize) {
    $_SESSION['evidence_error'] =
        'The selected file is larger than 10 MB.';

    header('Location: upload.php');
    exit;
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);

$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    'application/pdf' => 'pdf',
    'video/mp4' => 'mp4',
    'audio/mpeg' => 'mp3',
    'audio/wav' => 'wav',
    'audio/x-wav' => 'wav'
];

if (!isset($allowedTypes[$mimeType])) {
    $_SESSION['evidence_error'] =
        'This file type is not allowed.';

    header('Location: upload.php');
    exit;
}

$originalName = basename($file['name']);
$originalName = preg_replace(
    '/[^a-zA-Z0-9._ -]/',
    '',
    $originalName
);

if ($originalName === '') {
    $originalName = 'evidence.' . $allowedTypes[$mimeType];
}

$storedName = bin2hex(random_bytes(20))
    . '.'
    . $allowedTypes[$mimeType];

$uploadDirectory = dirname(__DIR__)
    . DIRECTORY_SEPARATOR
    . 'uploads'
    . DIRECTORY_SEPARATOR
    . 'evidence';

if (!is_dir($uploadDirectory)) {
    if (!mkdir($uploadDirectory, 0755, true)) {
        $_SESSION['evidence_error'] =
            'The upload folder could not be created.';

        header('Location: upload.php');
        exit;
    }
}

$destination = $uploadDirectory
    . DIRECTORY_SEPARATOR
    . $storedName;

if (!move_uploaded_file($file['tmp_name'], $destination)) {
    $_SESSION['evidence_error'] =
        'The evidence file could not be saved.';

    header('Location: upload.php');
    exit;
}

$alertId = filter_input(
    INPUT_POST,
    'alert_id',
    FILTER_VALIDATE_INT
);

if (!$alertId) {
    $alertId = null;
}

$userId = (int) $_SESSION['user_id'];

if ($alertId !== null) {
    $statement = $pdo->prepare(
        'SELECT alert_id
         FROM alerts
         WHERE alert_id = ? AND user_id = ?
         LIMIT 1'
    );

    $statement->execute([$alertId, $userId]);

    if (!$statement->fetch()) {
        unlink($destination);

        $_SESSION['evidence_error'] =
            'The selected alert is invalid.';

        header('Location: upload.php');
        exit;
    }
}

$relativePath = 'uploads/evidence/' . $storedName;

try {
    $statement = $pdo->prepare(
        'INSERT INTO evidence
        (
            user_id,
            alert_id,
            file_name,
            file_path,
            file_type,
            uploaded_at
        )
        VALUES (?, ?, ?, ?, ?, NOW())'
    );

    $statement->execute([
        $userId,
        $alertId,
        $originalName,
        $relativePath,
        $mimeType
    ]);

    $_SESSION['evidence_message'] =
        'Your evidence was uploaded securely.';

    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    if (is_file($destination)) {
        unlink($destination);
    }

    $_SESSION['evidence_error'] =
        'The evidence record could not be saved.';

    header('Location: upload.php');
    exit;
}