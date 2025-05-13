<?php
session_start();
include __DIR__ . "/../includes/db.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
        echo json_encode(["success" => false, "message" => "Unauthorized access."]);
        exit();
    }

    $user_id = $_SESSION["user_id"];
    $role = $_SESSION["role"];

    $response = ["success" => false, "message" => "Something went wrong."];

    if ($role === "student") {
        if (empty($_POST["enrollment_number"]) || empty($_POST["course"]) || empty($_POST["year"]) || empty($_POST["educator_id"])) {
            echo json_encode(["success" => false, "message" => "All fields are required."]);
            exit();
        }

        $enrollment_number = trim($_POST["enrollment_number"]);
        $course = trim($_POST["course"]);
        $year = intval($_POST["year"]);
        $section = isset($_POST["section"]) ? trim($_POST["section"]) : "";
        $educator_id = intval($_POST["educator_id"]);

        $check_sql = "SELECT id FROM student_profiles WHERE user_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            // Update existing profile
            $sql = "UPDATE student_profiles SET enrollment_number=?, course=?, year=?, section=?, educator_id=? WHERE user_id=?";
        } else {
            // Insert new profile
            $sql = "INSERT INTO student_profiles (user_id, enrollment_number, course, year, section, educator_id) VALUES (?, ?, ?, ?, ?, ?)";
        }
        $check_stmt->close();

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssissi", $user_id, $enrollment_number, $course, $year, $section, $educator_id);

        if ($stmt->execute()) {
            $response = ["success" => true, "message" => "Profile updated successfully!", "redirect" => "../dashboard/dashboard.php"];
        } else {
            $response["message"] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    } elseif ($role === "educator") {
        if (empty($_POST["department"]) || empty($_POST["designation"]) || empty($_POST["experience_years"])) {
            echo json_encode(["success" => false, "message" => "All fields are required."]);
            exit();
        }

        $department = trim($_POST["department"]);
        $designation = trim($_POST["designation"]);
        $experience_years = intval($_POST["experience_years"]);

        $check_sql = "SELECT id FROM educator_profiles WHERE user_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $sql = "UPDATE educator_profiles SET department=?, designation=?, experience_years=? WHERE user_id=?";
        } else {
            $sql = "INSERT INTO educator_profiles (user_id, department, designation, experience_years) VALUES (?, ?, ?, ?)";
        }
        $check_stmt->close();

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issi", $user_id, $department, $designation, $experience_years);

        if ($stmt->execute()) {
            $response = ["success" => true, "message" => "Profile updated successfully!", "redirect" => "../dashboard/dashboard.php"];
        } else {
            $response["message"] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $response["message"] = "Invalid user role.";
    }
} else {
    $response["message"] = "Invalid request method.";
}

echo json_encode($response);
$conn->close();
?>
