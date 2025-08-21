<?php

$db = new mysqli('localhost', 'root', '', 'real_estate_ci4');
if ($db->connect_error) {
    die('Connection failed: ' . $db->connect_error);
}

$result = $db->query('SELECT id, name, email, created_at FROM contacts ORDER BY created_at DESC LIMIT 5');
echo "Recent enquiries:\n";
while ($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . " | Name: " . $row['name'] . " | Email: " . $row['email'] . " | Date: " . $row['created_at'] . "\n";
}
$db->close();
