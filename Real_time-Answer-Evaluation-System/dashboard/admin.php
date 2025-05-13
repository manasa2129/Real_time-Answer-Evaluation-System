<?php
session_start();
include "../includes/db.php"; // Include your database connection file
ini_set('display_errors', 1);
error_reporting(E_ALL);


// Ensure only admin can access
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}


// Fetch users for pagination
if (isset($_GET['viewUsers'])) {
  $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $perPage = 10;  // Number of items per page
  $offset = ($page - 1) * $perPage;

  try {
      // Fetch users from database
      $query = "SELECT * FROM users LIMIT $perPage OFFSET $offset";
      $result = $db->query($query);

      if (!$result) {
          throw new Exception("Error executing query: " . implode(":", $db->errorInfo()));
      }

      $users = $result->fetchAll(PDO::FETCH_ASSOC);

      // Get total number of users for pagination
      $totalQuery = "SELECT COUNT(*) FROM users";
      $totalResult = $db->query($totalQuery);
      
      if (!$totalResult) {
          throw new Exception("Error executing query: " . implode(":", $db->errorInfo()));
      }

      $totalUsers = $totalResult->fetchColumn();
      $totalPages = ceil($totalUsers / $perPage);

      // Return data as JSON
      echo json_encode([
          "users" => $users,
          "totalPages" => $totalPages
      ]);
  } catch (Exception $e) {
      // In case of an error, output the error message
      echo json_encode(["error" => $e->getMessage()]);
  }
  exit();
}


// Fetch submissions for pagination
if (isset($_GET['viewSubmissions'])) {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $perPage = 10;  // Number of items per page
    $offset = ($page - 1) * $perPage;

    try {
        // Fetch submissions from database
        $query = "SELECT * FROM educator_uploads LIMIT $perPage OFFSET $offset";
        $result = $db->query($query);
        $submissions = $result->fetchAll(PDO::FETCH_ASSOC);

        // Get total number of submissions for pagination
        $totalQuery = "SELECT COUNT(*) FROM educator_uploads";
        $totalResult = $db->query($totalQuery);
        $totalSubmissions = $totalResult->fetchColumn();
        $totalPages = ceil($totalSubmissions / $perPage);

        // Return data as JSON
        echo json_encode([
            "submissions" => $submissions,
            "totalPages" => $totalPages
        ]);
    } catch (Exception $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            height: 100vh;
            background-color: #1a1a2e;
            color: white;
            padding: 1rem;
        }

        .sidebar a {
            color: #a9b3c1;
            text-decoration: none;
            display: block;
            padding: 0.5rem;
            border-radius: 5px;
        }

        .sidebar a:hover {
            background-color: #16213e;
        }

        .main-content {
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .table-responsive {
            max-height: 500px;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Navigation -->
            <nav class="col-md-2 d-none d-md-block sidebar">
                <h4>Admin Dashboard</h4>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="#viewUsers">View Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#viewSubmissions">View Submissions</a>
                    </li>
                </ul>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto">
                <!-- View Users Section -->
                <section id="viewUsers" class="main-content">
                    <h3>All Users</h3>
                    <div class="table-responsive">
                        <table class="table table-hover" id="userTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- User rows will be dynamically populated -->
                            </tbody>
                        </table>
                        <div id="userPagination"></div>
                    </div>
                </section>

                <!-- View Submissions Section -->
                <section id="viewSubmissions" class="main-content">
                    <h3>All Submissions</h3>
                    <div class="table-responsive">
                        <table class="table table-hover" id="submissionTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Filename</th>
                                    <th>Uploaded At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Submission rows will be dynamically populated -->
                            </tbody>
                        </table>
                        <div id="submissionPagination"></div>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <script>
        // Fetch and display users
        function fetchUsers(page = 1) {
            fetch('admin.php?viewUsers&page=' + page)
                .then(response => response.json())
                .then(data => {
                    const userTable = document.getElementById('userTable').querySelector('tbody');
                    const pagination = document.getElementById('userPagination');
                    userTable.innerHTML = '';
                    data.users.forEach(user => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${user.id}</td>
                            <td>${user.username}</td>
                            <td>${user.email}</td>
                            <td>${user.role}</td>
                            <td>${user.created_at}</td>
                            <td><button class="btn btn-danger btn-sm" onclick="deleteUser(${user.id})">Delete</button></td>
                        `;
                        userTable.appendChild(row);
                    });

                    // Pagination
                    pagination.innerHTML = '';
                    for (let i = 1; i <= data.totalPages; i++) {
                        const pageButton = document.createElement('button');
                        pageButton.classList.add('btn', 'btn-link');
                        pageButton.textContent = i;
                        pageButton.onclick = () => fetchUsers(i);
                        pagination.appendChild(pageButton);
                    }
                })
                .catch(error => console.error("Error fetching users:", error));
        }

        // Fetch and display submissions
        function fetchSubmissions(page = 1) {
            fetch('admin.php?viewSubmissions&page=' + page)
                .then(response => response.json())
                .then(data => {
                    const submissionTable = document.getElementById('submissionTable').querySelector('tbody');
                    const pagination = document.getElementById('submissionPagination');
                    submissionTable.innerHTML = '';
                    data.submissions.forEach(submission => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${submission.id}</td>
                            <td>${submission.username}</td>
                            <td>${submission.filename}</td>
                            <td>${submission.uploaded_at}</td>
                            <td><button class="btn btn-danger btn-sm" onclick="deleteSubmission(${submission.id})">Delete</button></td>
                        `;
                        submissionTable.appendChild(row);
                    });

                    // Pagination
                    pagination.innerHTML = '';
                    for (let i = 1; i <= data.totalPages; i++) {
                        const pageButton = document.createElement('button');
                        pageButton.classList.add('btn', 'btn-link');
                        pageButton.textContent = i;
                        pageButton.onclick = () => fetchSubmissions(i);
                        pagination.appendChild(pageButton);
                    }
                })
                .catch(error => console.error("Error fetching submissions:", error));
        }

        // Initialize dashboard
        document.addEventListener("DOMContentLoaded", function () {
            fetchUsers();
            fetchSubmissions();
        });
    </script>
</body>

</html>
