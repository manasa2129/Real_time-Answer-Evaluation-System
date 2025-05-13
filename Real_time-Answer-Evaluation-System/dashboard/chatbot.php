<?php
session_start();
include dirname(__DIR__) . "/includes/db.php";

if (!isset($_POST['msg']) || !isset($_SESSION["user_id"])) {
    echo "Error: Invalid request.";
    exit;
}

$message = strtolower(trim($_POST['msg']));
$user_id = $_SESSION["user_id"];

// Fetch student details
$query = "SELECT u.username, s.course 
          FROM users u 
          JOIN student_profiles s ON u.id = s.user_id
          WHERE s.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

$student_name = $student['username'] ?? "Student";
$course = $student['course'] ?? "your course";

// Default response
$response = "I'm not sure about that, $student_name.\n";
$response .= "Try asking about your course, evaluations, or progress.\n or kindly contact your professor\n";

// Personalized responses
if ($message == "hi" || $message == "hello") {
    $response = "Hello, $student_name!\n";
    $response .= "How can I assist you today?\n";
} elseif (strpos($message, "my name") !== false) {
    $response = "Your name is $student_name!\n";
} elseif (strpos($message, "course") !== false) {
    $response = "You are currently enrolled in $course.\n";
} elseif (strpos($message, "evaluation") !== false) {
    // Fetch latest evaluation
    $query = "SELECT id, upload_id, title, student_id, educator_id, 
                     grammar_score, relevance_score, overall_score, status, evaluated_at 
              FROM evaluations 
              WHERE student_id = ? 
              ORDER BY evaluated_at DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $evaluation_id = $row['id'];
        $upload_id = $row['upload_id'];
        $title = $row['title'];
        $educator_id = $row['educator_id'];
        $grammar = $row['grammar_score'];
        $relevance = $row['relevance_score'];
        $overall = $row['overall_score'];
        $status = $row['status'];
        $evaluated_at = $row['evaluated_at'];

        // Construct personalized feedback
        $response = "Your latest evaluation details:\n";
        $response .= "-----------------------------------\n";
        $response .= "Grammar Score: $grammar/100\n";
        $response .= "Relevance Score: $relevance/100\n";
        $response .= "Overall Score: $overall/100\n";
        $response .= "Status: $status\n";
        $response .= "Evaluated At: $evaluated_at\n";
        $response .= "-----------------------------------\n";

        // Score-based personalized feedback
        if ($overall > 85) {
            $response .= "🌟 Fantastic work, $student_name!\n";
            $response .= "Your writing is strong, and your understanding is excellent.\n";
            $response .= "Keep it up! 💪\n";
        } elseif ($overall > 60) {
            $response .= "👍 You're doing well, $student_name!\n";
            $response .= "Keep practicing to improve even further.\n";
            $response .= "Focus on refining your responses!\n";
        } else {
            $response .= "📚 Don't worry, $student_name.\n";
            $response .= "Your effort is valuable!\n";
            $response .= "Work on your grammar and relevance, and you'll see improvement! 🚀\n";
        }

        // Additional suggestions
        if ($grammar < 60) {
            $response .= "\n✍️ Grammar Tip:\n";
            $response .= "Try reviewing sentence structures and punctuation.\n";
            $response .= "Reading more can also help!\n";
        }
        if ($relevance < 60) {
            $response .= "\n🎯 Relevance Tip:\n";
            $response .= "Make sure your answers directly address the topic.\n";
            $response .= "Try to structure your thoughts clearly.\n";
        }
    } else {
        $response = "No evaluations found for you, $student_name.\n";
    }
} elseif (strpos($message, "progress") !== false) {
    // Fetch last three evaluations
    $query = "SELECT overall_score, evaluated_at FROM evaluations 
              WHERE student_id = ? 
              ORDER BY evaluated_at DESC LIMIT 3";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $scores = [];
    while ($row = $result->fetch_assoc()) {
        $scores[] = $row['overall_score'];
    }

    if (count($scores) > 1) {
        $trend = ($scores[0] > $scores[1]) ? "improving" : "needs improvement";
        $response = "📊 Your recent performance trend:\n";
        $response .= "Your scores indicate that your performance $trend.\n";
        $response .= "Keep up the great work, or focus on areas needing improvement! 💡\n";
    } else {
        $response = "Not enough data to analyze your progress yet, $student_name.\n";
    }
}

echo nl2br($response);
?>
