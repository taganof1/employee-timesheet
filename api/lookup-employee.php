<?php
header('Content-Type: application/json');

$uid = $_POST['uid'] ?? '';

if ($uid === '') {
    echo json_encode([
        'status' => 'error',
        'message' => 'No UID provided'
    ]);
    exit;
}

$filePath = __DIR__ . '/../employee-data/data.csv';
if (!file_exists($filePath)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Data file not found'
    ]);
    exit;
}

$rows = array_map('str_getcsv', file($filePath));

// Skip header row
array_shift($rows);

$found = false;
foreach ($rows as $row) {
    // Remove quotes from UID if they exist
    $csvUid = trim($row[0], '"');
    if ($csvUid === $uid) {
        $found = true;
        echo json_encode([
            'status' => 'success',
            'employeeId' => $row[1],
            'employeeName' => $row[2] . ' ' . $row[3]
        ]);
        break;
    }
}

if (!$found) {
    echo json_encode([
        'status' => 'error',
        'message' => 'UID not found'
    ]);
}
