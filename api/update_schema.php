<?php
include '../includes/db_connection.php';

// Read the SQL update
$sql = file_get_contents(__DIR__ . '/update_schema.sql');

// Split the SQL into individual statements
$statements = array_filter(array_map('trim', explode(';', $sql)));

$success = true;
foreach ($statements as $statement) {
    if (!empty($statement)) {
        if (!$conn->query($statement)) {
            echo "Error executing statement: " . $conn->error . "<br>";
            $success = false;
        }
    }
}

if ($success) {
    echo "Schema check completed successfully";
} else {
    echo "Some errors occurred during schema update";
}

$conn->close();
?> 