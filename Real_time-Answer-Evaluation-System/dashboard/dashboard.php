<?php
ob_start(); // Start output buffering
session_start();

// Ensure correct path for db.php
$file = __DIR__ . "/../includes/db.php";
if (!file_exists($file)) {
    die("Error: db.php not found at path: " . $file);
}
include $file;

// Redirect to login if user session is missing
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"];

// Check if profile is completed
if ($role === "student") {
    $sql = "SELECT id FROM student_profiles WHERE user_id = ?";
} else {
    $sql = "SELECT id FROM educator_profiles WHERE user_id = ?";
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    header("Location: ../auth/complete_profile.php");
    exit();
}

$stmt->close();

// Redirect to respective dashboards
if ($role === "student") {
    $redirect_url = "../dashboard/student.php";
} else {
    $redirect_url = "../dashboard/educator.php";
}

// Debugging: Check if headers are sent
if (headers_sent()) {
    echo "<script>window.location.href = '$redirect_url';</script>";
    exit();
} else {
    header("Location: $redirect_url");
    exit();
}

ob_end_flush(); // End output buffering
?>
