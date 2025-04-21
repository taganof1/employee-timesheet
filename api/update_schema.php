<?php
include '../includes/db_connection.php';

// Read SQL update
$sql = file_get_contents(__DIR__ . '/update_schema.sql'); //DIR used to get the absolute path to the file
if ($sql === false) {
    die("Error reading SQL file");
}
// Debugging (Check SQL statements execution)

// Split SQL into individual statements
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