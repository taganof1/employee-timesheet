<?php
session_start();
require_once '../includes/db_connection.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Check if user is authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get the record ID from the URL
$recordId = $_GET['id'] ?? null;
if (!$recordId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Record ID is required']);
    exit;
}

// Get the request data
$data = json_decode(file_get_contents('php://input'), true);

try {
    // Prepare the update query
    $query = "UPDATE employee_clocking 
              SET employee_id = ?, 
                  first_name = ?, 
                  last_name = ?, 
                  clocked_in_at = ?, 
                  clocked_out_at = ? 
              WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "issssi",
        $data['employee_id'],
        $data['first_name'],
        $data['last_name'],
        $data['clocked_in_at'],
        $data['clocked_out_at'],
        $recordId
    );
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Record updated successfully']);
    } else {
        throw new Exception("Error updating record: " . $stmt->error);
    }
    
    $stmt->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?> 