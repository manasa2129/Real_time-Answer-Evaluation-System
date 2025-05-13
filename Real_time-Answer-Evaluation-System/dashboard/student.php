<?php
session_start();
include dirname(__DIR__) . "/includes/db.php"; // Ensure correct path

// Ensure connection variable is set
if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . ($conn->connect_error ?? 'Unknown error'));
}

// Ensure user is logged in
if (!isset($_SESSION["user_id"])) {
    die("Error: User not logged in. Please login.");
}

$user_id = $_SESSION["user_id"];

// Fetch student and assigned educator details
$query = "SELECT u.username, u.email, s.enrollment_number, s.course, s.year, s.section, 
                 e.user_id AS educator_id, eu.username AS educator_name
          FROM users u
          JOIN student_profiles s ON u.id = s.user_id
          LEFT JOIN educator_profiles e ON s.educator_id = e.user_id
          LEFT JOIN users eu ON e.user_id = eu.id
          WHERE u.id = ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
} else {
    $student = [
        'username' => 'Not Found',
        'email' => 'Not Found',
        'enrollment_number' => 'Not Found',
        'course' => 'Not Found',
        'year' => 'Not Found',
        'section' => 'Not Found',
        'educator_name' => 'Not Assigned',
    ];
}

// Fetch evaluations for the logged-in student
$sql_evaluations = "SELECT e.id, e.upload_id, e.title, e.student_id, e.grammar_score, e.relevance_score, e.overall_score, e.status, e.evaluated_at
                    FROM evaluations e
                    WHERE e.student_id = ?";
$stmt_evaluations = $conn->prepare($sql_evaluations);
if (!$stmt_evaluations) {
    die("Query failed: " . $conn->error);
}
$stmt_evaluations->bind_param("i", $user_id);
$stmt_evaluations->execute();
$result_evaluations = $stmt_evaluations->get_result();

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f0f4f8; }
        .sidebar { height: auto; background-color: #1a1a2e; color: #fff; padding: 1rem; }
        .sidebar a { color: #a9b3c1; text-decoration: none; display: block; padding: 0.5rem 0; border-radius: 5px; }
        .sidebar a:hover { background-color: #16213e; }
        .main-content { padding: 2rem; background-color: #ffffff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }
        .btn-primary { background-color: #007bff; border: none; }
        .btn-primary:hover { background-color: #0056b3; }
        .chatbot-icon { position: fixed; bottom: 20px; right: 20px; background-color: #007bff; border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: white; font-size: 24px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); }
        .chatbox { position: fixed; bottom: 80px; right: 20px; width: 300px; background: white; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); display: none; }
        .chat-header { background: #007bff; color: white; padding: 10px; border-radius: 10px 10px 0 0; }
        .chat-body { max-height: 250px; overflow-y: auto; padding: 10px; }
        .chat-footer { padding: 10px; }
        .chat-footer input { width: 80%; padding: 5px; }
        .chat-footer button { width: 18%; background: #007bff; color: white; border: none; }
    
    </style>
    
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block sidebar">
            <a href="../Front.html"><h4 style="color:white;">Student Dashboard</h4></a>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="#dashboard">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="#evaluations">Uploaded Answers</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto">
            <!-- Dashboard Section -->
            <section id="dashboard" class="main-content">
                <h2>Welcome, <?php echo htmlspecialchars($student['username']); ?></h2>
                <p>Email: <?php echo htmlspecialchars($student['email']); ?></p>
                <p>Enrollment Number: <?php echo htmlspecialchars($student['enrollment_number']); ?></p>
                <p>Course: <?php echo htmlspecialchars($student['course']); ?></p>
                <p>Year: <?php echo htmlspecialchars($student['year']); ?></p>
                <p>Section: <?php echo htmlspecialchars($student['section']); ?></p>
                <b><p>Educator Name: <?php echo htmlspecialchars($student['educator_name']); ?></p></b>
            </section>

            <!-- Uploaded Answers Section -->
            <section id="evaluations" class="main-content">
                <h3 class="mt-5">Evaluations</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Grammar Score</th>
                            <th>Relevance Score</th>
                            <th>Overall Score</th>
                            <th>Status</th>
                            <th>Evaluated At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_evaluations->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['grammar_score']); ?></td>
                                <td><?php echo htmlspecialchars($row['relevance_score']); ?></td>
                                <td><?php echo htmlspecialchars($row['overall_score']); ?></td>
                                <td><span class="badge bg-<?php echo ($row['status'] == 'Reviewed' ? 'success' : 'warning'); ?>">
                                    <?php echo htmlspecialchars($row['status']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($row['evaluated_at']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </section>
<!-- Chatbot -->
<div class="chatbot-icon">💬</div>
<div class="chatbox" id="chatbox">
    <div class="chat-header">Chatbot <span  style="cursor:pointer; float:right;">✖</span></div>
    <div class="chat-body" id="chat-body"></div>
    <div class="chat-footer">
        <input type="text" id="chat-input" placeholder="Ask me anything...">
        <button>▶</button>
    </div>
</div>
        </main>
    </div>
</div>

<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    function toggleChat() {
        $("#chatbox").toggle();
    }
    function sendMessage() {
    var message = $("#chat-input").val().trim();
    if (message === "") return;

    $("#chat-body").append("<p><b>You:</b> " + message + "</p>");
    $("#chat-input").val("");

    $.ajax({
        type: "POST",
        url: "chatbot.php",
        data: { msg: message },
        success: function(response) {
            $("#chat-body").append("<p><b>Bot:</b> " + response + "</p>");
            $("#chat-body").scrollTop($("#chat-body")[0].scrollHeight);
        },
        error: function() {
            $("#chat-body").append("<p><b>Bot:</b> Error connecting to chatbot.</p>");
        }
    });
}


    // Attach event listeners instead of inline onclick
    $(".chatbot-icon, .chat-header span").click(toggleChat);
    $(".chat-footer button").click(sendMessage);

    // Enable Enter key to send messages
    $("#chat-input").keypress(function (e) {
        if (e.which === 13) { 
            sendMessage();
        }
    });
});
</script>





</body>
</html>
