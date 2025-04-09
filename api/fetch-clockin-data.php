<?php
header('Content-Type: application/json');
include '../includes/db_connection.php';

// Function to check if employee exists in data.csv
function checkEmployeeExists($uid) {
    $filePath = __DIR__ . '/../employee-data/data.csv';
    if (!file_exists($filePath)) {
        error_log("Data file not found: " . $filePath);
        return false;
    }

    $rows = array_map('str_getcsv', file($filePath));
    array_shift($rows); // Skip header row

    foreach ($rows as $row) {
        $csvUid = trim($row[0], '"');
        if ($csvUid === $uid) {
            return true;
        }
    }
    error_log("UID not found in data.csv: " . $uid);
    return false;
}

// Query to fetch latest clock-in data
$sql = "SELECT id, uid, employee_id, first_name, last_name, clocked_in_at FROM employee_clocking ORDER BY clocked_in_at DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    error_log("Latest clock-in UID: " . $row['uid']);
    
    // Check if employee exists in data.csv
    if (checkEmployeeExists($row['uid'])) {
        $response = [
            'status' => 'success',
            'uid' => $row['uid'],
            'employeeId' => $row['employee_id'],
            'employeeName' => $row['first_name'] . ' ' . $row['last_name'],
            'clockedInAt' => $row['clocked_in_at']
        ];
        error_log("Success response: " . json_encode($response));
        echo json_encode($response);
    } else {
        // If employee not found in CSV, delete the invalid record from database
        $deleteSql = "DELETE FROM employee_clocking WHERE id = ?";
        $stmt = $conn->prepare($deleteSql);
        $stmt->bind_param("i", $row['id']);
        $stmt->execute();
        $stmt->close();

        $response = [
            'status' => 'error',
            'message' => 'Employee not found in records',
            'uid' => $row['uid']
        ];
        error_log("Error response: " . json_encode($response));
        echo json_encode($response);
    }
} else {
    $response = [
        'status' => 'error',
        'message' => 'No recent clock-in records found',
        'uid' => null
    ];
    error_log("No records response: " . json_encode($response));
    echo json_encode($response);
}

$conn->close();
?>