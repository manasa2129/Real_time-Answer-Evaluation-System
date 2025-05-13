<?php
session_start();
include "../includes/db.php";

// Ensure the user is an educator
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "educator") {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $evaluation_id = $_POST["evaluation_id"] ?? null;
    $feedback = trim($_POST["feedback"] ?? '');
    $educator_id = $_SESSION["user_id"];

    if (!$evaluation_id || empty($feedback)) {
        echo "<script>alert('Invalid input. Please provide all required details.'); window.history.back();</script>";
        exit();
    }

    // Insert feedback into the `feedback` table
    $sql = "INSERT INTO feedback (evaluation_id, feedback_text, created_at) VALUES (?, ?, NOW())";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("is", $evaluation_id, $feedback);

        if ($stmt->execute()) {
            // Mark evaluation as reviewed
            $update_status = "UPDATE evaluations SET status='Reviewed' WHERE id=?";
            if ($stmt_update = $conn->prepare($update_status)) {
                $stmt_update->bind_param("i", $evaluation_id);
                $stmt_update->execute();
                $stmt_update->close();
            }

            echo "<script>alert('Feedback submitted successfully!'); window.location.href='dashboard.php#feedback';</script>";
        } else {
            echo "<script>alert('Error submitting feedback.'); window.history.back();</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Database error. Please try again later.'); window.history.back();</script>";
    }
}
?>
