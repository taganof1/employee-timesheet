<?php
include '../includes/db_connection.php';

// Check if UID is sent
if (!isset($_POST['uid'])) {
    die(json_encode(["status" => "error", "message" => "No UID received"]));
}

$uid = $_POST['uid'];

// Read employee data from the CSV
$csvFile = __DIR__ . "/../employee-data/data.csv"; // Ensure this file exists!
$employeeData = array_map('str_getcsv', file($csvFile));

// Find the employee by UID
$found = false;
foreach ($employeeData as $row) {
    if ($row[0] === $uid) { // UID matches
        $employee_id = $row[1];
        $first_name = $row[2];
        $last_name = $row[3];

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO employee_clocking (uid, employee_id, first_name, last_name) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $uid, $employee_id, $first_name, $last_name);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Clock-in recorded", "employee_id" => $employee_id, "name" => "$first_name $last_name"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to insert into database"]);
        }

        $stmt->close();
        $found = true;
        break;
    }
}

// If no matching UID was found
if (!$found) {
    echo json_encode(["status" => "error", "message" => "UID not found in employee records"]);
}

$conn->close();
?>
