<?php
include '../includes/db.php'; // Ensure DB connection

// Hardcoded values
$studentId = 1; // Hardcoded student ID
$educatorId = 31; // Hardcoded educator ID

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['answer_script']) && isset($_FILES['ground_truth'])) {
    
    $answerScript = $_FILES['answer_script'];
    $groundTruth = $_FILES['ground_truth'];

    // Check for upload errors
    if ($answerScript['error'] !== UPLOAD_ERR_OK || $groundTruth['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(["error" => "File upload error"]);
        exit;
    }

    // Get the next upload_id
    $result = $conn->query("SELECT MAX(id) AS max_id FROM educator_uploads");
    $row = $result->fetch_assoc();
    $uploadId = ($row['max_id'] ?? 0) + 1; // If null, start from 1

    // **Insert a dummy record into educator_uploads to satisfy FK constraint**
    $dummyFilename = "dummy_file_$uploadId.txt"; // Just a placeholder name
    $stmt = $conn->prepare("INSERT INTO educator_uploads (id, user_id, filename) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $uploadId, $educatorId, $dummyFilename);
    $stmt->execute();
    $stmt->close();

    // Prepare data for Flask API
    $postData = [
        'file' => new CURLFile($answerScript['tmp_name'], $answerScript['type'], $answerScript['name']),
        'groundtruth' => file_get_contents($groundTruth['tmp_name'])
    ];

    // Send request to Flask API
    $url = 'http://localhost:5000/predict';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        http_response_code(500);
        echo json_encode(["error" => "Flask request failed"]);
        exit;
    }

    $result = json_decode($response, true);

    if (!isset($result['wer'], $result['cer'], $result['similarity_score'], $result['levenshtein_distance'])) {
        http_response_code(500);
        echo json_encode(["error" => "Invalid Flask response"]);
        exit;
    }

    // Calculate scores
    $wer = $result['wer'];
    $cer = $result['cer'];
    $similarityScore = $result['similarity_score'];
    $levenshteinDistance = $result['levenshtein_distance'];

    $grammarScore = max(0, 100 - ($wer * 100)); // Example formula
    $relevanceScore = max(0, $similarityScore * 100); // Example formula
    $overallScore = ($grammarScore + $relevanceScore) / 2; // Example formula

    // Insert evaluation into database
    $stmt = $conn->prepare("
        INSERT INTO evaluations 
        (upload_id, student_id, educator_id, grammar_score, relevance_score, overall_score, status, evaluated_at) 
        VALUES (?, ?, ?, ?, ?, ?, 'Reviewed', NOW())
    ");
    $stmt->bind_param("iiiddd", $uploadId, $studentId, $educatorId, $grammarScore, $relevanceScore, $overallScore);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Evaluation inserted successfully!',
            'grammar_score' => $grammarScore,
            'relevance_score' => $relevanceScore,
            'overall_score' => $overallScore
        ]);
    } else {
        echo json_encode(["error" => "Database insertion failed"]);
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(["error" => "Invalid request"]);
}
