<?php

/**
 * Test script to verify cascade delete functionality
 *
 * This script checks if images are properly deleted when properties are removed.
 */

define('FCPATH', getcwd() . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

echo "=== Cascade Delete Test ===\n\n";

// Test images that should be deleted when their properties are removed
$testCases = [
    44 => ['uploads/properties/1754328912_190e979d2944222ec1b9.jpg'],
    47 => ['uploads/properties/1754330331_89fdb8e740977a7a80f8.jpg']
];

foreach ($testCases as $propertyId => $images) {
    echo "Property ID $propertyId:\n";

    foreach ($images as $imagePath) {
        $fullPath = FCPATH . $imagePath;
        if (file_exists($fullPath)) {
            echo "  ✓ Image exists: $imagePath\n";
        } else {
            echo "  ✗ Image missing: $imagePath\n";
        }
    }
    echo "\n";
}

echo "To test cascade delete:\n";
echo "1. Note which images exist above\n";
echo "2. Delete the properties through the web interface\n";
echo "3. Run this script again to verify images were deleted\n";
