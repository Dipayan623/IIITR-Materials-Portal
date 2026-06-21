<?php

require_once 'config/db.php';

if (
    !isset($_GET['branch_id']) ||
    !isset($_GET['semester'])
) {
    header("Location: index.php");
    exit();
}

$branch_id = intval($_GET['branch_id']);
$semester = intval($_GET['semester']);

/* FETCH BRANCH */

$stmt = $conn->prepare(
    "SELECT *
     FROM branches
     WHERE id = ?"
);

$stmt->bind_param("i", $branch_id);
$stmt->execute();

$branch = $stmt
    ->get_result()
    ->fetch_assoc();

if (!$branch) {
    header("Location: index.php");
    exit();
}

/* FETCH SUBJECTS */

$stmt = $conn->prepare(
    "SELECT *
    FROM subjects
    WHERE semester = ?
    AND (
        branch_id = ?
        OR branch_id = 1
    )
    ORDER BY subject_name
     ORDER BY subject_name ASC"
);

$stmt->bind_param(
    "ii",
    $branch_id,
    $semester
);

$stmt->execute();

$subjects = $stmt->get_result();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Select Subject</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
      rel="stylesheet">

<link rel="stylesheet"
      href="assets/css/style.css">

<link rel="stylesheet"
      href="assets/css/student.css">

</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

<div class="container">

    <a class="navbar-brand fw-bold"
       href="index.php">

       IIITR Study Materials

    </a>

    <a href="semester.php?branch_id=<?php echo $branch_id; ?>"
       class="btn btn-outline-light">

       Back

    </a>

</div>

</nav>

<section class="hero">

<div class="container">

<h1 class="display-5 fw-bold">

    <?php
    echo htmlspecialchars(
        $branch['branch_name']
    );
    ?>

</h1>

<p class="lead mt-3">

    Semester <?php echo $semester; ?>

</p>

<hr class="my-5">

<h3 class="selection-title">

    Select Subject

</h3>

<div class="row justify-content-center">

    <?php if(
        $subjects->num_rows > 0
    ): ?>

        <?php while(
            $subject =
            $subjects->fetch_assoc()
        ): ?>

        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">

            <a

            href="materials.php?subject_id=<?php
            echo $subject['id'];
            ?>"

            class="text-decoration-none">

                <div
                class="card selection-card p-4 text-center">

                    <div class="card-body">

                        <h4 class="text-dark mb-0">

                            <?php
                            echo htmlspecialchars(
                            $subject['subject_name']
                            );
                            ?>

                        </h4>

                    </div>

                </div>

            </a>

        </div>

        <?php endwhile; ?>

    <?php else: ?>

        <div class="col-12">

            <div class="alert alert-warning">

                No subjects found
                for this semester.

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
