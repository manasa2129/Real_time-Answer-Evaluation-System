<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database credentials
$servername = "localhost";  
$username = "root";         
$password = "";             
$database = "real_time_evaluation_system"; 

// Create MySQLi connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Optional: Remove this after testing
// echo "Database connected successfully!";
?>
