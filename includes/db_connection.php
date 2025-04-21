<?php
// Read config from JSON file
$configFile = __DIR__ . '/../config/config.json';
if (!file_exists($configFile)) {
    die("Configuration file not found");
}

$config = json_decode(file_get_contents($configFile), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error reading configuration file");
}

// Extract data from config for use
$dbConfig = $config['database'] ?? null;
if (!$dbConfig) {
    die("Database configuration not found");
}

// New mysqli connection
$conn = new mysqli(
    $dbConfig['host'],
    $dbConfig['username'],
    $dbConfig['password'],
    $dbConfig['dbname']
);

// Error handling
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>