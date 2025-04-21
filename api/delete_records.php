<?php
session_start();
require_once '../includes/db_connection.php';

header('Content-Type: application/json');

// Check if user exists
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);
$recordIds = $data['recordIds'] ?? [];

if (empty($recordIds)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No records selected']);
    exit;
}

try {
    // Create list of placeholders for prepared statement
    $placeholders = str_repeat('?,', count($recordIds) - 1) . '?';
    
    // Delete record
    $query = "DELETE FROM employee_clocking WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($query);
    
    // Bind parameters
    $types = str_repeat('i', count($recordIds));
    $stmt->bind_param($types, ...$recordIds);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Records deleted successfully']);
    } else {
        throw new Exception("Error deleting records: " . $stmt->error);
    }
    
    $stmt->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?> 