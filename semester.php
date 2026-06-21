<?php

require_once 'config/db.php';

if (!isset($_GET['branch_id'])) {
    header("Location: index.php");
    exit();
}

$branch_id = intval($_GET['branch_id']);

/* FETCH BRANCH */

$stmt = $conn->prepare(
    "SELECT *
     FROM branches
     WHERE id = ?"
);

$stmt->bind_param("i", $branch_id);
$stmt->execute();

$result = $stmt->get_result();
$branch = $result->fetch_assoc();

if (!$branch) {
    header("Location: index.php");
    exit();
}

/* FETCH AVAILABLE SEMESTERS */

$stmt = $conn->prepare(
    "SELECT DISTINCT semester
     FROM subjects
     WHERE branch_id = ?
     ORDER BY semester ASC"
);

$stmt->bind_param("i", $branch_id);
$stmt->execute();

$semesters = $stmt->get_result();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Select Semester</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/student.css">

</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

<div class="container">

    <a class="navbar-brand fw-bold" href="index.php">
        IIITR Study Materials
    </a>

    <a href="index.php" class="btn btn-outline-light">
        Back
    </a>

</div>

</nav>

<section class="hero">

<div class="container">

    <h1 class="display-4 fw-bold">

        <?php echo htmlspecialchars($branch['branch_name']); ?>

    </h1>

    <p class="lead mt-3">
        Select Semester
    </p>

    <hr class="my-5">

    <div class="row justify-content-center">

        <?php if($semesters->num_rows > 0): ?>

            <?php while($semester = $semesters->fetch_assoc()): ?>

                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">

                    <a
                        href="subject.php?branch_id=<?php echo $branch_id; ?>&semester=<?php echo $semester['semester']; ?>"
                        class="text-decoration-none">

                        <div class="card selection-card p-4 text-center">

                            <div class="card-body">

                                <h2 class="text-dark mb-0">

                                    Semester
                                    <?php echo $semester['semester']; ?>

                                </h2>

                            </div>

                        </div>

                    </a>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="col-12">

                <div class="alert alert-warning">

                    No semesters found for this branch.

                </div>

            </div>

        <?php endif; ?>

    </div>

</div>

</section>

<footer class="text-center py-4 border-top">

<small>
    © <?php echo date("Y"); ?>
    IIIT Ranchi Study Materials
</small>

</footer>

<script src="assets/js/main.js"></script>

</body>
</html>
