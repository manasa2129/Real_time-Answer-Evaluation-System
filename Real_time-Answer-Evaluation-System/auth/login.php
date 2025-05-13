<?php
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Debugging step
echo "Step 1: Script started.<br>";

include __DIR__ . "/../includes/db.php";

// Ensure database connection exists
if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . ($conn->connect_error ?? 'Unknown error.'));
}

echo "Step 2: Database connected successfully.<br>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = strtolower(trim($_POST["username"])); // Convert to lowercase
    $password = trim($_POST["password"]);
    $role = strtolower(trim($_POST["role"])); // Convert role to lowercase

    echo "Step 3: POST Data Received.<br>";

    // Validate input
    if (empty($username) || empty($password) || empty($role)) {
        die("Step 4: Error - All fields are required.");
    }

    // Fetch user from database
    $sql = "SELECT id, username, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Step 5: SQL Error: " . $conn->error);
    }

    echo "Step 6: Query prepared successfully.<br>";

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    echo "Step 7: Query executed. Rows found: " . $stmt->num_rows . "<br>";

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $db_username, $db_password, $db_role);
        $stmt->fetch();

        echo "Step 8: User found. Database Role: $db_role.<br>";

        if ($db_role !== $role) {
            die("Step 9: Role mismatch. Selected: $role, Found: $db_role");
        }

        // Verify password
        if (password_verify($password, $db_password)) {
            echo "Step 10: Password verification successful.<br>";

            session_regenerate_id(true);
            $_SESSION["user_id"] = $id;
            $_SESSION["username"] = $db_username;
            $_SESSION["role"] = $db_role;

            // Redirect based on role
            $redirect_url = ($db_role === "admin") ? "../dashboard/admin.php" :
                            ($db_role === "educator" ? "../dashboard/educator.php" : "../dashboard/student.php");

            echo "Step 11: Redirecting to: $redirect_url<br>";
            
            header("Location: $redirect_url");
            exit();
        } else {
            die("Step 12: Incorrect password.");
        }
    } else {
        die("Step 13: User not found in database.");
    }

    $stmt->close();
}

$conn->close();
?>
