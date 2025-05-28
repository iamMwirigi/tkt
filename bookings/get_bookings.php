<?php
// session_start(); // Uncomment if you need session-based authentication/authorization for this endpoint

require_once '../config/db.php'; // Assuming 'db.php' is the correct filename
require_once '../utils/functions.php'; // To use sendResponse

header('Access-Control-Allow-Origin: *'); // Adjust as needed for your CORS policy. Should be set before any output.

$db = new Database(); // This should match the class name in your db.php
$pdo = $db->getConnection();

try {
    // Fetch all bookings
    $stmt = $pdo->prepare("
        SELECT 
            b.*,  -- All columns from bookings
            t.departure_time, -- Example related data
            t.trip_code
        FROM bookings b
        INNER JOIN trips t ON b.trip_id = t.id
    "); 
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($bookings) {
        sendResponse(200, ['success' => true, 'bookings' => $bookings]);
    } else {
        sendResponse(200, ['success' => true, 'message' => 'No bookings found']);
    }

} catch (PDOException $e) {
    // Log the detailed error for server-side review
    error_log("Database Error in get_bookings.php: " . $e->getMessage());
    sendResponse(500, ['error' => 'An internal server error occurred. Please try again later.']);
}
?>