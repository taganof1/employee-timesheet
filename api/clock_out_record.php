<?php
session_start();
require_once '../includes/db_connection.php';

header('Content-Type: application/json');

// Check if user has been authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Get record ID from URL request
$recordId = $_GET['id'] ?? null;
if (!$recordId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Record ID is required']);
    exit;
}

try {
    // Check if record records relating to UID exists and isn't clcocked out
    $checkQuery = "SELECT id FROM employee_clocking WHERE id = ? AND clocked_out_at IS NULL";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("i", $recordId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Record not found or already clocked out']);
        exit;
    }
    
    // Update record to add clock out time onto record
    $updateQuery = "UPDATE employee_clocking SET clocked_out_at = NOW() WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("i", $recordId);
    
    if ($updateStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Record clocked out successfully']);
    } else {
        throw new Exception("Error clocking out record: " . $updateStmt->error);
    }
    
    $updateStmt->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?> 