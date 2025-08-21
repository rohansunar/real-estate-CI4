<?php

/**
 * Test script to verify enquiry delete functionality
 */

echo "=== Testing Enquiry Delete Functionality ===\n\n";

// Test the delete endpoint using cURL
$enquiryId = 58; // Test with enquiry ID 58
$url = "http://localhost:8081/dashboard/enquiries/$enquiryId";

echo "Testing DELETE request to: $url\n";

// Initialize cURL
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-Requested-With: XMLHttpRequest'
]);

// Execute the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "HTTP Status Code: $httpCode\n";

if ($error) {
    echo "cURL Error: $error\n";
} else {
    echo "Response: $response\n";
}

// Check if enquiry was actually deleted
echo "\nVerifying deletion...\n";

$db = new mysqli('localhost', 'root', '', 'real_estate_ci4');
if ($db->connect_error) {
    die('Connection failed: ' . $db->connect_error);
}

$result = $db->query("SELECT id FROM contacts WHERE id = $enquiryId");
if ($result->num_rows === 0) {
    echo "✅ SUCCESS: Enquiry $enquiryId was successfully deleted\n";
} else {
    echo "❌ FAILED: Enquiry $enquiryId still exists in database\n";
}

$db->close();

echo "\nTest completed.\n";
