<?php
// This script is used exclusively for the mobile web app.

// Set headers to allow cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Check if it's a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the UID from the POST data
    $uid = isset($_POST['uid']) ? $_POST['uid'] : '';
    
    if (empty($uid)) {
        echo json_encode(["status" => "error", "message" => "No UID provided"]);
        exit;
    }
    
    // Write the UID to the same file that the Python script reads
    $uidFile = "../uid.txt";
    file_put_contents($uidFile, $uid);
    
    // Return success response
    echo json_encode(["status" => "success", "message" => "UID received: $uid"]);
} else {
    // Return error for non-POST requests
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?> 