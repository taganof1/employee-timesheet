<?php
session_start();
require_once '../includes/db_connection.php';

// Set headers for JSON response
header('Content-Type: application/json');

// Check if this is a password verification request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the password from the request
    $data = json_decode(file_get_contents('php://input'), true);
    $password = $data['password'] ?? '';
    
    // This should be replaced with your actual secure password
    // In a production environment, this should be stored securely and hashed
    $correctPassword = "admin123"; // Replace this with your desired password
    
    if ($password === $correctPassword) {
        $_SESSION['authenticated'] = true;
        echo json_encode(['success' => true]);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid password']);
    }
    exit;
}

// For GET requests, check if user is authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

try {
    // Prepare and execute the query
    $query = "SELECT id, uid, employee_id, first_name, last_name, clocked_in_at, clocked_out_at 
              FROM employee_clocking 
              ORDER BY clocked_in_at DESC";
    
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception("Error fetching records: " . $conn->error);
    }
    
    // Fetch all records
    $records = [];
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
    
    // Return the records as JSON
    echo json_encode($records);
    
} catch (Exception $e) {
    // Return error response
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

// Close the database connection
$conn->close();
?> 