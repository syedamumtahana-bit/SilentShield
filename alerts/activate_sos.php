<?php
session_start();

header('Content-Type: application/json');

require_once '../config/db.php';
require_once '../includes/csrf.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Authentication required.'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid method.'
    ]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!is_array($data)) {
    $data = [];
}

if (!verify_csrf_token($data['csrf_token'] ?? null)) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid security token.'
    ]);
    exit;
}

$latitude = isset($data['latitude']) && is_numeric($data['latitude'])
    ? (float) $data['latitude']
    : null;

$longitude = isset($data['longitude']) && is_numeric($data['longitude'])
    ? (float) $data['longitude']
    : null;

$location = 'Location unavailable';

if ($latitude !== null && $longitude !== null) {
    $location = $latitude . ', ' . $longitude;
}

try {
    $statement = $pdo->prepare(
        'INSERT INTO alerts
        (
            user_id,
            alert_time,
            latitude,
            longitude,
            location,
            status,
            message
        )
        VALUES (?, NOW(), ?, ?, ?, ?, ?)'
    );

    $statement->execute([
        (int) $_SESSION['user_id'],
        $latitude,
        $longitude,
        $location,
        'active',
        'Emergency SOS activated'
    ]);

    echo json_encode([
        'success' => true,
        'alert_id' => (int) $pdo->lastInsertId()
    ]);
} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'The alert could not be recorded.'
    ]);
}