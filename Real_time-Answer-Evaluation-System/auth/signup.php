<?php
session_start(); // Start session
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure db.php is included correctly
include __DIR__ . "/../includes/db.php";

// Check database connection
if (!isset($conn)) {
    die(json_encode(["success" => false, "message" => "Error: Database connection failed."]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate inputs
    if (empty($_POST["username"]) || empty($_POST["email"]) || empty($_POST["password"]) || empty($_POST["role"])) {
        die(json_encode(["success" => false, "message" => "Error: All fields are required."]));
    }

    // Trim and sanitize input
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
    $role = trim($_POST["role"]);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die(json_encode(["success" => false, "message" => "Error: Invalid email format."]));
    }

    // Check if username or email already exists
    $check_sql = "SELECT id FROM users WHERE username = ? OR email = ?";
    $check_stmt = $conn->prepare($check_sql);
    
    if (!$check_stmt) {
        die(json_encode(["success" => false, "message" => "Error preparing statement: " . $conn->error]));
    }

    $check_stmt->bind_param("ss", $username, $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        die(json_encode(["success" => false, "message" => "Error: Username or email already exists."]));
    }
    $check_stmt->close();

    // Insert new user
    $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die(json_encode(["success" => false, "message" => "Error preparing statement: " . $conn->error]));
    }

    $stmt->bind_param("ssss", $username, $email, $password, $role);

    if ($stmt->execute()) {
        $_SESSION["user_id"] = $stmt->insert_id;
        $_SESSION["role"] = $role;

        echo json_encode(["success" => true, "message" => "Signup successful. Redirecting..."]);
        echo "<script>window.location.href = '../auth/complete_profile.php';</script>";
        exit();
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Error: Invalid request method."]);
}

$conn->close();
?>
