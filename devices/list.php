<?php
require_once '../config/db.php';
require_once '../utils/functions.php';

header('Content-Type: application/json');

// Check authentication
$user_id = checkAuth();

$db = new Database();
$conn = $db->getConnection();

try {
    $stmt = $conn->prepare("
        SELECT id, device_uuid, device_name, is_active, created_at 
        FROM devices 
        WHERE user_id = ?
    ");
    $stmt->execute([$user_id]);
    $devices = $stmt->fetchAll();

    sendResponse(200, [
        'message' => 'Devices retrieved successfully',
        'devices' => $devices
    ]);

} catch (PDOException $e) {
    sendResponse(500, ['error' => 'Database error: ' . $e->getMessage()]);
}
?> 