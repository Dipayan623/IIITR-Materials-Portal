<?php

require_once 'config/db.php';

if (!isset($_GET['subject_id'])) {
    header("Location: index.php");
    exit();
}

$subject_id = intval($_GET['subject_id']);

/* SUBJECT DETAILS */

$stmt = $conn->prepare(
    "SELECT s.*, b.branch_name
     FROM subjects s
     LEFT JOIN branches b
     ON s.branch_id = b.id
     WHERE s.id = ?"
);

$stmt->bind_param("i", $subject_id);
$stmt->execute();

$subject = $stmt->get_result()->fetch_assoc();

if (!$subject) {
    header("Location: index.php");
    exit();
}

/* NOTES */

$notes = $conn->prepare(
    "SELECT *
     FROM materials
     WHERE subject_id = ?
     AND material_type = 'Notes'
     ORDER BY title"
);

$notes->bind_param("i", $subject_id);
$notes->execute();
$notes_result = $notes->get_result();

/* BOOKS */

$books = $conn->prepare(
    "SELECT *
     FROM materials
     WHERE subject_id = ?
     AND material_type = 'Book'
     ORDER BY title"
);

$books->bind_param("i", $subject_id);
$books->execute();
$books_result = $books->get_result();

/* ASSIGNMENTS */

$assignments = $conn->prepare(
    "SELECT *
     FROM materials
     WHERE subject_id = ?
     AND material_type = 'Assignment'
     ORDER BY title"
);

$assignments->bind_param("i", $subject_id);
$assignments->execute();
$assignments_result = $assignments->get_result();

/* LABS */

$labs = $conn->prepare(
    "SELECT *
     FROM materials
     WHERE subject_id = ?
     AND material_type = 'Lab'
     ORDER BY title"
);

$labs->bind_param("i", $subject_id);
$labs->execute();
$labs_result = $labs->get_result();

/* PYQS GROUPED BY YEAR */

$pyqs = $conn->prepare(
    "SELECT
        m.*,
        ay.year_name
     FROM materials m
     LEFT JOIN academic_years ay
     ON m.academic_year_id = ay.id
     WHERE m.subject_id = ?
     AND m.material_type = 'PYQ'
     ORDER BY ay.year_name DESC, m.title ASC"
);

$pyqs->bind_param("i", $subject_id);
$pyqs->execute();
$pyqs_result = $pyqs->get_result();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>
<?php echo htmlspecialchars($subject['subject_name']); ?>
</title>

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

<a href="javascript:history.back()"
class="btn btn-outline-light">
Back </a>

</div>

</nav>

<div class="container py-5">

<h1 class="mb-2">
<?php echo htmlspecialchars($subject['subject_name']); ?>
</h1>

<p class="text-muted">
<?php echo htmlspecialchars($subject['branch_name']); ?>
 |
Semester <?php echo $subject['semester']; ?>
</p>

<ul class="nav nav-tabs" id="materialTabs" role="tablist">

<li class="nav-item">
<button class="nav-link active"
data-bs-toggle="tab"
data-bs-target="#notes">
Notes
</button>
</li>

<li class="nav-item">
<button class="nav-link"
data-bs-toggle="tab"
data-bs-target="#books">
Books
</button>
</li>

<li class="nav-item">
<button class="nav-link"
data-bs-toggle="tab"
data-bs-target="#pyqs">
PYQs
</button>
</li>

<li class="nav-item">
<button class="nav-link"
data-bs-toggle="tab"
data-bs-target="#assignments">
Assignments
</button>
</li>

<li class="nav-item">
<button class="nav-link"
data-bs-toggle="tab"
data-bs-target="#labs">
Labs
</button>
</li>

</ul>

<div class="tab-content border border-top-0 p-4 bg-white">

<!-- NOTES -->

<div class="tab-pane fade show active" id="notes">

<?php if($notes_result->num_rows > 0): ?>

<?php while($row = $notes_result->fetch_assoc()): ?>

<div class="mb-2">

<a href="<?php echo htmlspecialchars($row['file_path']); ?>"
target="_blank">

<?php echo htmlspecialchars($row['title']); ?>

</a>

</div>

<?php endwhile; ?>

<?php else: ?>

<p>No notes available.</p>

<?php endif; ?>

</div>

<!-- BOOKS -->

<div class="tab-pane fade" id="books">

<?php if($books_result->num_rows > 0): ?>

<?php while($row = $books_result->fetch_assoc()): ?>

<div class="mb-2">

<a href="<?php echo htmlspecialchars($row['file_path']); ?>"
target="_blank">

<?php echo htmlspecialchars($row['title']); ?>

</a>

</div>

<?php endwhile; ?>

<?php else: ?>

<p>No books available.</p>

<?php endif; ?>

</div>

<!-- PYQS -->

<div class="tab-pane fade" id="pyqs">

<?php

$currentYear = '';

if($pyqs_result->num_rows > 0):

while($row = $pyqs_result->fetch_assoc()):

if($currentYear != $row['year_name']):

$currentYear = $row['year_name'];

?>

<h4 class="mt-4 mb-3">
<?php echo htmlspecialchars($currentYear); ?>
</h4>

<?php endif; ?>

<div class="mb-2 ms-3">

<a href="<?php echo htmlspecialchars($row['file_path']); ?>"
target="_blank">

<?php echo htmlspecialchars($row['title']); ?>

</a>

</div>

<?php
endwhile;

else:
?>

<p>No PYQs available.</p>

<?php endif; ?>

</div>

<!-- ASSIGNMENTS -->

<div class="tab-pane fade" id="assignments">

<?php if($assignments_result->num_rows > 0): ?>

<?php while($row = $assignments_result->fetch_assoc()): ?>

<div class="mb-2">

<a href="<?php echo htmlspecialchars($row['file_path']); ?>"
target="_blank">

<?php echo htmlspecialchars($row['title']); ?>

</a>

</div>

<?php endwhile; ?>

<?php else: ?>

<p>No assignments available.</p>

<?php endif; ?>

</div>

<!-- LABS -->

<div class="tab-pane fade" id="labs">

<?php if($labs_result->num_rows > 0): ?>

<?php while($row = $labs_result->fetch_assoc()): ?>

<div class="mb-2">

<a href="<?php echo htmlspecialchars($row['file_path']); ?>"
target="_blank">

<?php echo htmlspecialchars($row['title']); ?>

</a>

</div>

<?php endwhile; ?>

<?php else: ?>

<p>No lab materials available.</p>

<?php endif; ?>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
