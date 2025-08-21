<?php

/**
 * Test cascade delete by directly deleting from database
 * Note: This bypasses the PropertyModel callbacks, so cascade delete won't work
 * This is to demonstrate the difference between direct DB delete vs Model delete
 */

// Test cascade delete by deleting property 44
$db = new mysqli('localhost', 'root', '', 'real_estate_ci4');

if ($db->connect_error) {
    die('Connection failed: ' . $db->connect_error);
}

// First, verify the property and image exist
$result = $db->query('SELECT id, title, images FROM properties WHERE id = 44');
$property = $result->fetch_assoc();

if (!$property) {
    echo "Property 44 not found\n";
    exit(1);
}

echo "Property 44: " . $property['title'] . "\n";
echo "Images: " . $property['images'] . "\n";

// Check if image file exists
$images = json_decode($property['images'], true);
$imagePath = $images[0];
$fullPath = 'public/' . $imagePath;

echo "Image path: " . $imagePath . "\n";
echo "Full path: " . $fullPath . "\n";
echo "Image exists before delete: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";

echo "\nNOTE: Direct database delete bypasses PropertyModel callbacks\n";
echo "This means cascade delete will NOT work with direct SQL\n";
echo "The image should still exist after this delete\n\n";

// Delete the property directly from database (bypasses Model callbacks)
$deleteResult = $db->query('DELETE FROM properties WHERE id = 44');

if ($deleteResult) {
    echo "Property deleted from database\n";
    
    // Check if image still exists
    echo "Image exists after delete: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
    
    if (file_exists($fullPath)) {
        echo "EXPECTED: Image still exists because direct DB delete bypasses cascade delete callbacks\n";
    } else {
        echo "UNEXPECTED: Image was deleted (this shouldn't happen with direct DB delete)\n";
    }
} else {
    echo "Failed to delete property: " . $db->error . "\n";
}

$db->close();
