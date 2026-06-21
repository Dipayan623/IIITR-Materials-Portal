<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../config/db.php';

/* DELETE FILE */

if (isset($_GET['delete'])) {

    $id = intval($_GET['delete']);

    $stmt = $conn->prepare(
        "SELECT file_path
         FROM materials
         WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {

        $fullPath = "../" . $row['file_path'];

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        $delete = $conn->prepare(
            "DELETE FROM materials
             WHERE id = ?"
        );

        $delete->bind_param("i", $id);
        $delete->execute();

        /* RESET AUTO_INCREMENT */

        $result = $conn->query(
            "SELECT COALESCE(MAX(id),0) + 1 AS next_id
             FROM materials"
        );

        $next = $result->fetch_assoc();

        $nextId = $next['next_id'];

        $conn->query(
            "ALTER TABLE materials
             AUTO_INCREMENT = $nextId"
        );
    }

    header("Location: manage_files.php");
    exit();
}

/* LOAD FILES */

$sql = "

SELECT

m.id,
m.title,
m.material_type,
m.semester,
m.file_name,
m.file_path,
m.uploaded_at,

ay.year_name,
b.branch_name,
s.subject_name

FROM materials m

LEFT JOIN academic_years ay
ON m.academic_year_id = ay.id

LEFT JOIN branches b
ON m.branch_id = b.id

LEFT JOIN subjects s
ON m.subject_id = s.id

ORDER BY m.id DESC

";

$materials = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manage Files</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>Manage Files</h2>

        <a href="dashboard.php"
           class="btn btn-secondary">
            Dashboard
        </a>

    </div>

    <div class="card shadow">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover table-striped align-middle">

                    <thead>

                    <tr>

                        <th>ID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Branch</th>
                        <th>Semester</th>
                        <th>Subject</th>
                        <th>Year</th>
                        <th>File</th>
                        <th>Uploaded</th>
                        <th>Actions</th>

                    </tr>

                    </thead>

                    <tbody>

                    <?php while ($file = $materials->fetch_assoc()) : ?>

                    <tr>

                        <td><?= $file['id']; ?></td>

                        <td>
                            <?= htmlspecialchars($file['title']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($file['material_type']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($file['branch_name']); ?>
                        </td>

                        <td>
                            Semester <?= $file['semester']; ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($file['subject_name']); ?>
                        </td>

                        <td>
                            <?= $file['year_name'] ?: '-'; ?>
                        </td>

                        <td>

                        <a
                        href="../<?= htmlspecialchars($file['file_path']); ?>"
                        target="_blank">

                        <?= htmlspecialchars($file['file_name']); ?>

                        </a>

                        </td>

                        <td>

                        <?= date(
                            "d M Y, h:i A",
                            strtotime(
                                $file['uploaded_at']
                            )
                        ); ?>

                        </td>

                        <td>

                            <a
                                href="edit_file.php?id=<?= $file['id']; ?>"
                                class="btn btn-warning btn-sm">
                                Edit
                            </a>

                            <a
                                href="?delete=<?= $file['id']; ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Delete this file?');">
                                Delete
                            </a>

                        </td>

                    </tr>

                    <?php endwhile; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<script src="../assets/js/admin.js"></script>

</body>
</html>