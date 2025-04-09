<?php
// Set headers for JSON response
header('Content-Type: application/json');

// Get the mode from the request
$data = json_decode(file_get_contents('php://input'), true);
$mode = $data['mode'] ?? '';

// Validate mode
if (!in_array($mode, ['clockIn', 'clockOut'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid mode']);
    exit;
}

// Write the mode to a file that the Python script can read
$modeFile = __DIR__ . '/../current_mode.txt';
file_put_contents($modeFile, $mode);

// Return success response
echo json_encode(['status' => 'success', 'message' => "Mode set to $mode"]);
?> 