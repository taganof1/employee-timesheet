<?php
include 'db_connection.php'; // Include the database connection

// Query to fetch latest clock-in data
$sql = "SELECT id, uid, employee_id, first_name, last_name, clocked_in_at FROM employee_clocking ORDER BY clocked_in_at DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['uid']}</td>
                <td>{$row['employee_id']}</td>
                <td>{$row['first_name']} {$row['last_name']}</td>
                <td>{$row['clocked_in_at']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='5'>No records found</td></tr>";
}

$conn->close();
?>