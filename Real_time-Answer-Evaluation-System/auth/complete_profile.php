<?php
session_start(); // Start session at the beginning
include __DIR__ . "/../includes/db.php"; // Include database connection

// Redirect to login if session is missing
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = htmlspecialchars($_SESSION["user_id"]);
$user_role = htmlspecialchars($_SESSION["role"]);


// Fetch educators for student selection
echo "<script>console.log('Fetching Educators');</script>";
$educatorQuery = "SELECT u.id, u.username, e.department, e.experience_years FROM users u 
                  JOIN educator_profiles e ON u.id = e.user_id WHERE u.role = 'educator'";
$educators = $conn->query($educatorQuery);

// Fetch existing profile data (if any)
$profileQuery = ($user_role === "student") ? 
    "SELECT * FROM student_profiles WHERE user_id = ?" :
    "SELECT * FROM educator_profiles WHERE user_id = ?";

$stmt = $conn->prepare($profileQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$profileData = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Profile</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <style>
    body {
    background: url("https://informationage-production.s3.amazonaws.com/uploads/2023/03/ChatGPT-future-of-AI-scaled.jpg") no-repeat center center/cover;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    margin: 0;
    padding: 20px;
    position: relative;
}

/* Darker overlay for better readability */
body::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: -1;
}

/* Form container styling */
.container {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    width: 400px;
    text-align: center;
    transition: transform 0.2s ease-in-out;
}

.container:hover {
    transform: scale(1.02);
}

/* Headings */
h2 {
    margin-bottom: 15px;
    color: #333;
}

/* Labels and Inputs */
label {
    display: block;
    margin: 10px 0 5px;
    font-weight: bold;
    color: #444;
    text-align: left;
}

input {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
    outline: none;
    transition: border-color 0.2s ease-in-out;
}

input:focus {
    border-color: #007bff;
}

/* Submit Button */
button {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 6px;
    background-color: #007bff;
    color: white;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.2s ease-in-out;
}
select {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
    outline: none;
    background: white;
    cursor: pointer;
    transition: border-color 0.2s ease-in-out, background 0.2s ease-in-out;
}

select:hover {
    border-color: #007bff;
}

select:focus {
    border-color: #0056b3;
    background: #f8f9fa;
}

/* Style for dropdown options */
select option {
    padding: 10px;
    font-size: 16px;
    background: white;
}

button:hover {
    background-color: #0056b3;
}

/* Response message */
#responseMessage {
    margin-top: 15px;
    font-weight: bold;
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .container {
        width: 90%;
    }
}
</style>
</head>
<body>
    <div class="container">
        <h2>Complete Your Profile</h2>
        <form id="profileForm">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <input type="hidden" name="role" value="<?php echo $user_role; ?>">

            <?php if ($user_role === "student") { ?>
                <label for="enrollment_number">Enrollment Number</label>
                <input type="text" name="enrollment_number" id="enrollment_number" value="<?php echo $profileData['enrollment_number'] ?? ''; ?>" required>

                <label for="course">Course</label>
                <input type="text" name="course" id="course" value="<?php echo $profileData['course'] ?? ''; ?>" required>

                <label for="year">Year</label>
                <?php $yearValue = isset($profileData['year']) ? htmlspecialchars($profileData['year']) : ''; ?>
                <input type="number" name="year" id="year" min="1" max="5" step="1" value="<?= $yearValue ?>" required>
                
                <label for="section">Section</label>
                <input type="text" name="section" id="section" value="<?php echo $profileData['section'] ?? ''; ?>" required>

                <label for="educator">Educator</label>
                <select name="educator_id" id="educator" required>
                    <option value="">-- Select an Educator --</option>
                    <?php while ($row = $educators->fetch_assoc()) { ?>
                        <option value="<?php echo $row['id']; ?>" <?php echo ($profileData['educator_id'] ?? '') == $row['id'] ? 'selected' : ''; ?>>
                            <?php echo $row['username']; ?> (<?php echo $row['department']; ?>, <?php echo $row['experience_years']; ?> yrs)
                        </option>
                    <?php } ?>
                </select>
            <?php } else { ?>
                <label for="department">Department</label>
                <input type="text" name="department" id="department" value="<?php echo $profileData['department'] ?? ''; ?>" required>

                <label for="designation">Designation</label>
                <input type="text" name="designation" id="designation" value="<?php echo $profileData['designation'] ?? ''; ?>" required>

                <label for="experience_years">Years of Experience</label>
                <input type="number" name="experience_years" id="experience_years" min="0" value="<?php echo $profileData['experience_years'] ?? ''; ?>" required>
            <?php } ?>

            <button type="submit">Save Profile</button>
        </form>
        <div id="responseMessage"></div>
    </div>

    <script>
   document.getElementById("profileForm").addEventListener("submit", function(event) {
    event.preventDefault();

    let formData = new FormData(this);

    fetch("../auth/save_profile.php", { method: "POST", body: formData })
        .then(response => response.json())
        .then(data => {
            console.log("Server Response:", data);

            document.getElementById("responseMessage").innerHTML = data.message;
            document.getElementById("responseMessage").style.color = data.success ? "green" : "red";

            if (data.success) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            }
        })
        .catch(error => {
            console.error("Fetch Error:", error);
            document.getElementById("responseMessage").innerHTML = "Something went wrong. Please try again.";
        });
});

    </script>
</body>
</html>