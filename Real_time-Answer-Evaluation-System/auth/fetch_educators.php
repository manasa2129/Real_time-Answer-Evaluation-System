<?php
include __DIR__ . "/../includes/db.php";

$course = isset($_GET['course']) ? $_GET['course'] : '';

// Fetch educators based on course match
$sql = "SELECT id, username, department, experience_years FROM users WHERE role = 'educator' AND department LIKE ?";
$stmt = $conn->prepare($sql);
$search = "%$course%";
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();

$educators = [];
while ($row = $result->fetch_assoc()) {
    $educators[] = $row;
}

echo json_encode($educators);
?>
