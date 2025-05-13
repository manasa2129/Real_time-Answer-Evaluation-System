
<?php
session_start();
include "../includes/db.php"; // Ensure database connection

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "educator") {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch educator details
$sql = "SELECT u.username, e.department, e.designation, e.experience_years 
        FROM users u 
        JOIN educator_profiles e ON u.id = e.user_id 
        WHERE u.id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Query failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($educator_name, $department, $designation, $experience_years);
$stmt->fetch();
$stmt->close();

// Handle file upload (Answer Scripts)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["answerscript"])) {
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $filename = basename($_FILES["answerscript"]["name"]);
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($_FILES["answerscript"]["tmp_name"], $filepath)) {
        $stmt = $conn->prepare("INSERT INTO uploads (user_id, filename, uploaded_at) VALUES (?, ?, NOW())");
        if (!$stmt) {
            die("Query failed: " . $conn->error);
        }
        $stmt->bind_param("is", $user_id, $filename);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch assigned students
$sql_students = "SELECT u.username, s.enrollment_number, s.course, s.year, s.section 
                 FROM users u
                 JOIN student_profiles s ON u.id = s.user_id
                 WHERE s.educator_id = ?";
$stmt_students = $conn->prepare($sql_students);
if (!$stmt_students) {
    die("Query failed: " . $conn->error);
}
$stmt_students->bind_param("i", $user_id);
$stmt_students->execute();
$result_students = $stmt_students->get_result();

// Fetch uploaded question banks
$sql_questionbanks = "SELECT filename, uploaded_at FROM educator_uploads WHERE user_id = ?";
$stmt_questionbanks = $conn->prepare($sql_questionbanks);
if (!$stmt_questionbanks) {
    die("Query failed: " . $conn->error);
}
$stmt_questionbanks->bind_param("i", $user_id);
$stmt_questionbanks->execute();
$result_questionbanks = $stmt_questionbanks->get_result();

// Fetch evaluations
$sql_evaluations = "SELECT e.id, e.upload_id, e.title, e.student_id, e.grammar_score, e.relevance_score, e.overall_score, e.status, e.evaluated_at
                    FROM evaluations e
                    WHERE e.educator_id = ?";
$stmt_evaluations = $conn->prepare($sql_evaluations);
if (!$stmt_evaluations) {
    die("Query failed: " . $conn->error);
}
$stmt_evaluations->bind_param("i", $user_id);
$stmt_evaluations->execute();
$result_evaluations = $stmt_evaluations->get_result();
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Educator Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Arial', sans-serif;
      background-color: #f0f4f8;
    }
    .sidebar {
      height: auto;
      background-color: #1a1a2e;
      color: #fff;
      padding: 1rem;
    }
    .sidebar a {
      color: #a9b3c1;
      text-decoration: none;
      display: block;
      padding: 0.5rem 0;
      border-radius: 5px;
    }
    .sidebar a:hover {
      background-color: #16213e;
    }
    .sidebar h4 {
      font-weight: bold;
      margin-bottom: 2rem;
    }
    .main-content {
      padding: 2rem;
      background-color: #ffffff;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      margin-bottom: 2rem;
    }
    .btn-primary {
      background-color: #007bff;
      border: none;
    }
    .btn-primary:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar Navigation -->
      <nav class="col-md-2 d-none d-md-block sidebar">
        <a href="../Front.html"><h4 style="color:white;">Educator Dashboard</h4></a>
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link" href="#dashboard">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="#students">Assigned Students</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#ans-scripts">Answer Scripts Upload</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#uploadFile">Uploaded Files</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#evaluations">Evaluations</a>
          </li>
        </ul>
      </nav>

      <!-- Main Content Area -->
      <main class="col-md-10 ms-sm-auto">
        <section id="dashboard" class="main-content">
            <h3>Welcome, <?php echo htmlspecialchars($educator_name); ?></h3>
            <p><strong>Department:</strong> <?php echo htmlspecialchars($department); ?></p>
            <p><strong>Designation:</strong> <?php echo htmlspecialchars($designation); ?></p>
            <p><strong>Years of Experience:</strong> <?php echo htmlspecialchars($experience_years); ?> years</p>
          
          <p>Manage student submissions, provide feedback, and view analytics all in one place.</p>
        </section>
         <!-- Assigned Students Section -->
         <section id="students" class="p-3">
          <h3>Assigned Students</h3>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Name</th>
                <th>Enrollment No.</th>
                <th>Course</th>
                <th>Year</th>
                <th>Section</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $result_students->fetch_assoc()) { ?>
                <tr>
                  <td><?php echo htmlspecialchars($row['username']); ?></td>
                  <td><?php echo htmlspecialchars($row['enrollment_number']); ?></td>
                  <td><?php echo htmlspecialchars($row['course']); ?></td>
                  <td><?php echo htmlspecialchars($row['year']); ?></td>
                  <td><?php echo htmlspecialchars($row['section']); ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </section>

 
      <!--Answer scripts-->
      <section id="ans-scripts" class="main-content">
    <h3>Upload Answer Scripts</h3>
    <form id="answerScriptForm" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="answerscript" class="form-label">Choose Answer Script</label>
            <input type="file" class="form-control" id="answerscript" name="answer_script" required>
        </div>
        <div class="mb-3">
            <label for="groundtruth" class="form-label">Choose Ground Truth (Text File)</label>
            <input type="file" class="form-control" id="groundtruth" name="ground_truth" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload & Evaluate</button>
    </form>

    <div id="evaluationResults" class="mt-3"></div>
</section>
      <!-- <section id="ans-scripts"  class="main-content">
    <h3>Upload Answer Scripts</h3>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="answerscript" class="form-label">Choose File</label>
            <input type="file" class="form-control" id="answerscript" name="answerscript" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
    </form>
  </section> -->

  <!-- Uploaded Answer Scripts List -->
<!-- Uploaded Files List -->
<section class="main-content">
        <h3>Uploaded Files</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Filename</th>
                    <th>Uploaded At</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT filename, uploaded_at FROM educator_uploads WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['filename']) . "</td>
                            <td>" . htmlspecialchars($row['uploaded_at']) . "</td>
                          </tr>";
                }
                $stmt->close();
                ?>
            </tbody>
        </table>
    </section>
    

<!-- Evaluations -->

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
                    <td><span class="badge bg-<?php echo ($row['status'] == 'Reviewed' ? 'success' : 'warning'); ?>"><?php echo htmlspecialchars($row['status']); ?></span></td>
                    <td><?php echo htmlspecialchars($row['evaluated_at']); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    </section>

      </main>
    </div>
  </div>
  <script>
    function uploadFile() {
    let fileInput = document.getElementById("fileUpload");
    let file = fileInput.files[0];
    if (!file) {
        alert("Please select a file.");
        return;
    }

    let formData = new FormData();
    formData.append("fileUpload", file);

    document.getElementById("uploadStatus").innerHTML = "Uploading...";

    fetch("upload.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById("uploadStatus").innerHTML = data; // Display server response
    })
    .catch(error => {
        document.getElementById("uploadStatus").innerHTML = "Upload failed: " + error;
    });
}
  </script>
  <script>
    document.getElementById('answerScriptForm').addEventListener('submit', async function(event) {
        event.preventDefault(); // Prevent default form submission

        const formData = new FormData(this);

        try {
            const response = await fetch('evaluate.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (response.ok) {
                console.log('Predicted Text:', result.predicted_text);
                console.log('Similarity Score:', result.similarity_score);
                console.log('WER:', result.wer);
                console.log('CER:', result.cer);
                console.log('Levenshtein Distance:', result.levenshtein_distance);
            } else {
                console.error('Error:', result.error);
            }
        } catch (error) {
            console.error('Request failed:', error);
        }
    });
  </script>
  <!-- <script>
 async function submitForEvaluation() {
    const formData = new FormData();
    const answerScript = document.getElementById('answerscript').files[0];
    const groundTruth = document.getElementById('groundtruth').files[0];

    if (!answerScript || !groundTruth) {
        alert("Please select both files.");
        return;
    }

    formData.append("answer_script", answerScript);
    formData.append("ground_truth", groundTruth);

    try {
        const response = await fetch('http://127.0.0.1:5000/predict', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`Error: ${response.statusText}`);
        }

        const result = await response.json();
        console.log(result);

        // Display evaluation results
        document.getElementById('evaluationResults').innerHTML = `
            <div class="alert alert-success">
                <strong>Evaluation Results:</strong><br>
                <strong>Similarity Score:</strong> ${result.similarity_score}%<br>
                <strong>Word Error Rate (WER):</strong> ${result.wer}%<br>
                <strong>Character Error Rate (CER):</strong> ${result.cer}%<br>
                <strong>Levenshtein Distance:</strong> ${result.levenshtein_distance}
            </div>
        `;
    } catch (error) {
        console.error("Evaluation failed:", error);
        document.getElementById('evaluationResults').innerHTML = `
            <div class="alert alert-danger">Failed to evaluate. ${error.message}</div>
        `;
    }
}
  </script> -->
</body>
</html>