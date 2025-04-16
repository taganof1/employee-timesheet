<?php
// Check if the script is being run from command line
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line');
}

// Get password from command line argument
if ($argc < 2) {
    die("Usage: php set_admin_password.php <password>\n");
}

$password = $argv[1];

// Hash the password
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Read the current config
$configFile = __DIR__ . '/config.json';
if (!file_exists($configFile)) {
    die("Configuration file not found\n");
}

$config = json_decode(file_get_contents($configFile), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error reading configuration file\n");
}

// Update the password hash
$config['admin']['password_hash'] = $passwordHash;

// Write back to the config file
if (file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT))) {
    echo "Admin password updated successfully\n";
} else {
    die("Error writing to configuration file\n");
}
?> 