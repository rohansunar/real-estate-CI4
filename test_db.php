<?php

// Test database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'real_estate_ci4';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    echo "Database connection successful!\n";
    echo "Database: $database\n";
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}
