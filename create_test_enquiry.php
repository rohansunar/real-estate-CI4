<?php

$db = new mysqli('localhost', 'root', '', 'real_estate_ci4');
if ($db->connect_error) {
    die('Connection failed: ' . $db->connect_error);
}

// Create a test enquiry with all required fields
$stmt = $db->prepare('INSERT INTO contacts (name, email, phone, properties_in, message, is_read) VALUES (?, ?, ?, ?, ?, ?)');
$name = 'Test Delete User';
$email = 'test.delete@example.com';
$phone = '9999999999';
$properties_in = 'Siliguri';
$message = 'This is a test enquiry for delete functionality testing';
$is_read = 0;

$stmt->bind_param('sssssi', $name, $email, $phone, $properties_in, $message, $is_read);

if ($stmt->execute()) {
    $enquiryId = $db->insert_id;
    echo "Created test enquiry with ID: $enquiryId\n";
    echo "Name: $name\n";
    echo "Email: $email\n";
    echo "Phone: $phone\n";
    echo "Properties in: $properties_in\n";
} else {
    echo "Failed to create test enquiry: " . $stmt->error . "\n";
}

$stmt->close();
$db->close();
