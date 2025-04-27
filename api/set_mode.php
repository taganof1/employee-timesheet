<?php

header('Content-Type: application/json');

// Get clock in or clock out mode from request
$data = json_decode(file_get_contents('php://input'), true);
$mode = $data['mode'] ?? '';

// Validate mode
if (!in_array($mode, ['clockIn', 'clockOut'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid mode']);
    exit;
}

// Write mode to file for listener.py to use
$modeFile = __DIR__ . '/../current_mode.txt';
file_put_contents($modeFile, $mode);

// Debugging (Return success response)
echo json_encode(['status' => 'success', 'message' => "Mode set to $mode"]);
?> 