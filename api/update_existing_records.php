<?php
// OBSOLITE FILE
// This file is no longer used in the current version of the application.
// It was used to update existing records in the database to set the source to 'rfid'.
// The current version uses a different approach to handle clock-in and clock-out records.
// This file is kept for reference and may be removed in future versions.

include '../includes/db_connection.php';

// Update all existing records to have source = 'rfid'
$updateSql = "UPDATE employee_clocking SET source = 'rfid' WHERE source IS NULL OR source = 'manual'";
if ($conn->query($updateSql)) {
    echo "Successfully updated existing records to have source = 'rfid'<br>";
    
    // Show count of updated records
    $countSql = "SELECT COUNT(*) as count FROM employee_clocking WHERE source = 'rfid'";
    $result = $conn->query($countSql);
    if ($result) {
        $row = $result->fetch_assoc();
        echo "Total records with source = 'rfid': " . $row['count'] . "<br>";
    }
} else {
    echo "Error updating records: " . $conn->error;
}

$conn->close();
?> 