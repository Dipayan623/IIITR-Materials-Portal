<?php

require_once 'config/db.php';

/* FETCH BRANCHES */

$branches = $conn->query(
    "SELECT *
     FROM branches
     ORDER BY branch_name ASC"
);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="data:,">
<title>IIIT Ranchi Study Materials</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Custom CSS -->
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/student.css">

</head>

<body>

<!-- Navbar -->

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

    <div class="container">

        <a class="navbar-brand fw-bold" href="index.php">
            IIITR Study Materials
        </a>

        <a href="login.php" class="btn btn-outline-light">
            Admin Login
        </a>

    </div>

</nav>

<!-- Hero Section -->

<section class="hero">

    <div class="container">

        <h1 class="display-4 fw-bold">
            IIIT Ranchi Study Materials
        </h1>

        <p class="lead mt-3">
            Previous Year Question Papers, Notes, Books and Study Resources
        </p>

        <hr class="my-5">

        <h3 class="selection-title">
            Select Branch
        </h3>

        <div class="row justify-content-center">

            <?php if($branches->num_rows > 0): ?>

                <?php while($branch = $branches->fetch_assoc()): ?>

                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">

                        <a
                            href="semester.php?branch_id=<?php echo $branch['id']; ?>"
                            class="text-decoration-none">

                            <div class="card selection-card p-4 text-center">

                                <div class="card-body">

                                    <h2 class="text-dark mb-0">

                                        <?php
                                        echo htmlspecialchars(
                                            $branch['branch_name']
                                        );
                                        ?>

                                    </h2>

                                </div>

                            </div>

                        </a>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <div class="col-12">

                    <div class="alert alert-warning">

                        No branches available.

                    </div>

                </div>

            <?php endif; ?>

        </div>

    </div>

</section>

<!-- Footer -->

<footer class="text-center py-4 border-top">

    <small>
        © <?php echo date("Y"); ?>
        IIIT Ranchi Study Materials
    </small>

</footer>

<script src="assets/js/main.js"></script>

</body>
</html>
