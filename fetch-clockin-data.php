<?php
header('Content-Type: application/json');
include 'db_connection.php';

// Query to fetch latest clock-in data
$sql = "SELECT id, uid, employee_id, first_name, last_name, clocked_in_at FROM employee_clocking ORDER BY clocked_in_at DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        'status' => 'success',
        'uid' => $row['uid'],
        'employeeId' => $row['employee_id'],
        'employeeName' => $row['first_name'] . ' ' . $row['last_name'],
        'clockedInAt' => $row['clocked_in_at']
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Employee not found',
        'uid' => null
    ]);
}

$conn->close();
?>