<?php

// Create a test property with uploaded images
$db = new mysqli('localhost', 'root', '', 'real_estate_ci4');

if ($db->connect_error) {
    die('Connection failed: ' . $db->connect_error);
}

// Create test image files
$testImages = [
    'uploads/properties/test_cascade_1.jpg',
    'uploads/properties/test_cascade_2.jpg'
];

foreach ($testImages as $imagePath) {
    $fullPath = 'public/' . $imagePath;
    $dir = dirname($fullPath);
    
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    file_put_contents($fullPath, 'test image content for cascade delete testing');
    echo "Created test image: $imagePath\n";
}

// Insert test property
$title = 'Test Property for Cascade Delete';
$description = 'This property is created to test cascade delete functionality';
$type = 'house';
$location = 'Test Location';
$area = 1000;
$is_featured = 0;
$images = json_encode($testImages);
$youtube_video = json_encode([]);

$query = "INSERT INTO properties (title, description, type, location, area, is_featured, images, youtube_video) VALUES ('$title', '$description', '$type', '$location', $area, $is_featured, '$images', '$youtube_video')";
$result = $db->query($query);

if ($result) {
    $propertyId = $db->insert_id;
    echo "Created test property with ID: $propertyId\n";
    echo "Images: $images\n";
} else {
    echo "Failed to create test property: " . $db->error . "\n";
}
$db->close();
