<?php
session_start();
include "./includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $evaluation_id = $_POST["evaluation_id"];
    $feedback_text = $_POST["feedback_text"];

    $sql = "INSERT INTO feedback (evaluation_id, feedback_text) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $evaluation_id, $feedback_text);

    if ($stmt->execute()) {
        echo "Feedback submitted successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
