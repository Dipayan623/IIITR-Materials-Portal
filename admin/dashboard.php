<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">

</head>

<body>

    <!-- Header -->

    <div class="dashboard-header">

        <div class="container d-flex justify-content-between align-items-center">

            <div>

                <h2>Admin Dashboard</h2>

                <small>
                    Logged in as:
                    <?php echo htmlspecialchars($_SESSION['roll_number']); ?>
                </small>

            </div>

            <div>

                <a href="logout.php"
                   class="btn btn-danger"
                   onclick="return confirm('Are you sure you want to logout?');">

                    Logout

                </a>

            </div>

        </div>

    </div>

    <!-- Dashboard Content -->

    <div class="container mt-5">

        <div class="row g-4">

            <!-- Add Year -->

            <div class="col-md-4">

                <a href="add_year.php" class="text-decoration-none">

                    <div class="card action-card p-4">

                        <h4>Add Academic Year</h4>

                        <p class="text-muted">
                            Add new academic sessions.
                        </p>

                    </div>

                </a>

            </div>

            <!-- Add Branch -->

            <div class="col-md-4">

                <a href="add_branch.php" class="text-decoration-none">

                    <div class="card action-card p-4">

                        <h4>Add Branch</h4>

                        <p class="text-muted">
                            Add CSE, ECE, IT and other branches.
                        </p>

                    </div>

                </a>

            </div>

            <!-- Add Subject -->

            <div class="col-md-4">

                <a href="add_subject.php" class="text-decoration-none">

                    <div class="card action-card p-4">

                        <h4>Add Subject</h4>

                        <p class="text-muted">
                            Create subjects for semesters.
                        </p>

                    </div>

                </a>

            </div>

            <!-- Upload File -->

            <div class="col-md-4">

                <a href="upload_file.php" class="text-decoration-none">

                    <div class="card action-card p-4">

                        <h4>Upload Material</h4>

                        <p class="text-muted">
                            Upload PYQs, Notes and Books.
                        </p>

                    </div>

                </a>

            </div>

            <!-- Manage Files -->

            <div class="col-md-4">

                <a href="manage_files.php" class="text-decoration-none">

                    <div class="card action-card p-4">

                        <h4>Manage Files</h4>

                        <p class="text-muted">
                            Edit or delete uploaded materials.
                        </p>

                    </div>

                </a>

            </div>

            <!-- View Website -->

            <div class="col-md-4">

                <a href="../index.php" class="text-decoration-none">

                    <div class="card action-card p-4">

                        <h4>View Website</h4>

                        <p class="text-muted">
                            Open the student portal.
                        </p>

                    </div>

                </a>

            </div>

        </div>

    </div>

    <!-- JS -->

    <script src="../assets/js/admin.js"></script>

</body>

</html>