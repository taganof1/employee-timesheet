<?php
require_once '../includes/db_connection.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Check if this is a direct request or a polling request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug logging
    error_log("Clock out request received");
    error_log("Content-Type: " . ($_SERVER["CONTENT_TYPE"] ?? 'not set'));
    error_log("POST data: " . print_r($_POST, true));
    error_log("Raw input: " . file_get_contents('php://input'));

    // Check if the request is JSON or form data
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    
    if (strpos($contentType, "application/json") !== false) {
        // JSON request
        $data = json_decode(file_get_contents('php://input'), true);
        $uid = $data['uid'] ?? '';
        error_log("Processing JSON request. UID: " . $uid);
    } else {
        // Form data request
        $uid = $_POST['uid'] ?? '';
        error_log("Processing form data request. UID: " . $uid);
    }
} else {
    // Polling request - check for new UID in the database
    // This would be similar to how fetch-clockin-data.php works
    // For now, we'll just return a simple response
    echo json_encode([
        'status' => 'waiting',
        'message' => 'Waiting for RFID card to be scanned'
    ]);
    exit;
}

if (empty($uid)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'UID is required']);
    exit;
}

try {
    // Debug logging for SQL query
    error_log("Looking for clock-in record with UID: " . $uid);
    
    // First, find the most recent clock-in record for this UID that hasn't been clocked out
    $findQuery = "SELECT id, employee_id, first_name, last_name, clocked_in_at 
                  FROM employee_clocking 
                  WHERE uid = ? AND clocked_out_at IS NULL 
                  ORDER BY clocked_in_at DESC 
                  LIMIT 1";
    
    $stmt = $conn->prepare($findQuery);
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        error_log("No active clock-in found for UID: " . $uid);
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'No active clock-in found for this employee'
        ]);
        exit;
    }
    
    $record = $result->fetch_assoc();
    error_log("Found record: " . print_r($record, true));
    
    // Update the record with clock-out time
    $updateQuery = "UPDATE employee_clocking 
                   SET clocked_out_at = NOW() 
                   WHERE id = ?";
    
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("i", $record['id']);
    
    if ($updateStmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Successfully clocked out',
            'data' => [
                'employeeId' => $record['employee_id'],
                'employeeName' => $record['first_name'] . ' ' . $record['last_name'],
                'clockedInAt' => $record['clocked_in_at'],
                'clockedOutAt' => date('Y-m-d H:i:s')
            ]
        ]);
    } else {
        throw new Exception("Error updating clock-out time");
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?> 