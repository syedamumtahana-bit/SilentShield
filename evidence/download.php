<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/db.php';

$evidenceId = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$evidenceId) {
    http_response_code(400);
    exit('Invalid evidence file.');
}

$statement = $pdo->prepare(
    'SELECT file_name, file_path, file_type
     FROM evidence
     WHERE evidence_id = ? AND user_id = ?
     LIMIT 1'
);

$statement->execute([
    $evidenceId,
    (int) $_SESSION['user_id']
]);

$file = $statement->fetch();

if (!$file) {
    http_response_code(404);
    exit('Evidence file not found.');
}

$projectDirectory = dirname(__DIR__);
$fullPath = $projectDirectory
    . DIRECTORY_SEPARATOR
    . str_replace(
        ['/', '\\'],
        DIRECTORY_SEPARATOR,
        $file['file_path']
    );

$realProjectDirectory = realpath($projectDirectory);
$realFilePath = realpath($fullPath);

if (
    $realProjectDirectory === false
    || $realFilePath === false
    || !str_starts_with($realFilePath, $realProjectDirectory)
    || !is_file($realFilePath)
) {
    http_response_code(404);
    exit('Evidence file is unavailable.');
}

$downloadName = str_replace(
    ['"', "\r", "\n"],
    '',
    $file['file_name']
);

header('Content-Type: ' . $file['file_type']);
header(
    'Content-Disposition: attachment; filename="'
    . $downloadName
    . '"'
);
header('Content-Length: ' . filesize($realFilePath));
header('X-Content-Type-Options: nosniff');

readfile($realFilePath);
exit;