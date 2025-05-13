<?php
session_start();
include "../includes/db.php"; // Ensure database connection

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "educator") {
    echo "Unauthorized access!";
    exit();
}

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["fileUpload"])) {
    $file_name = basename($_FILES["fileUpload"]["name"]);
    $target_dir = "../uploads/";
    $target_file = $target_dir . $file_name;

    // Ensure the uploads directory exists
    if (!is_dir($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
            echo "Failed to create upload directory!";
            exit();
        }
    }

    // Check for upload errors
    if ($_FILES["fileUpload"]["error"] > 0) {
        echo "File upload error: " . $_FILES["fileUpload"]["error"];
        exit();
    }

    // Move file to server directory
    if (move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $target_file)) {
        // Insert into database
        $sql = "INSERT INTO educator_uploads (user_id, filename) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo "Database error: " . $conn->error;
            exit();
        }
        $stmt->bind_param("is", $user_id, $file_name);

        if ($stmt->execute()) {
            echo "File uploaded successfully!";
        } else {
            echo "Database error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "File upload failed!";
    }
} else {
    echo "Invalid request!";
}
?>