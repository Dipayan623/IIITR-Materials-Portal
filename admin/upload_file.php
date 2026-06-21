<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../config/db.php';

$message = "";
$error = "";

$years = $conn->query(
    "SELECT * FROM academic_years ORDER BY year_name DESC"
);

$branches = $conn->query(
    "SELECT * FROM branches ORDER BY branch_name"
);

$subjects = $conn->query(
    "SELECT * FROM subjects ORDER BY subject_name"
);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title = trim($_POST['title']);
    $material_type = $_POST['material_type'];

    $academic_year_id = null;

    if ($material_type === "PYQ") {
        $academic_year_id = intval($_POST['academic_year_id']);
    }

    $semester = intval($_POST['semester']);
    $branch_id = intval($_POST['branch_id']);
    $subject_id = intval($_POST['subject_id']);

    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {

        $file = $_FILES['pdf_file'];

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if ($extension !== 'pdf' || $mimeType !== 'application/pdf') {
            $error = "Only valid PDF files are allowed.";
        } else {

            $newFileName = bin2hex(random_bytes(8)) . "_" . basename($_FILES['pdf_file']['name']);
            $targetPath = "../uploads/" . $newFileName;

            if (move_uploaded_file(
                $file['tmp_name'],
                $targetPath
            )) {

                $dbPath = "uploads/" . $newFileName;

                $stmt = $conn->prepare(
                    "INSERT INTO materials
                    (
                        title,
                        material_type,
                        academic_year_id,
                        semester,
                        branch_id,
                        subject_id,
                        file_name,
                        file_path
                    )
                    VALUES
                    (?, ?, ?, ?, ?, ?, ?, ?)"
                );

                $stmt->bind_param(
                    "ssiiiiss",
                    $title,
                    $material_type,
                    $academic_year_id,
                    $semester,
                    $branch_id,
                    $subject_id,
                    $newFileName,
                    $dbPath
                );

                if ($stmt->execute()) {

                    $message = "Material uploaded successfully.";

                } else {

                    $error = "Database insert failed.";
                }

            } else {

                $error = "Failed to upload file.";
            }
        }
    }
}

?>

<!DOCTYPE html>

<html>
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Upload Material</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

#yearSection{
    display:none;
}

</style>

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Upload Material</h2>

    <a href="dashboard.php" class="btn btn-secondary">
        Dashboard
    </a>

</div>

<?php if($message){ ?>
    <div class="alert alert-success">
        <?php echo $message; ?>
    </div>
<?php } ?>

<?php if($error){ ?>
    <div class="alert alert-danger">
        <?php echo $error; ?>
    </div>
<?php } ?>

<div class="card">

    <div class="card-body">

        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">

                <label class="form-label">
                    Title
                </label>

                <input
                    type="text"
                    name="title"
                    class="form-control"
                    required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Material Type
                </label>

                <select
                    name="material_type"
                    class="form-select">

                    <option value="Notes" selected>
                        Notes
                    </option>

                    <option value="Book">
                        Book
                    </option>

                    <option value="Assignment">
                        Assignment
                    </option>

                    <option value="Lab">
                        Lab
                    </option>

                    <option value="PYQ">
                        PYQ
                    </option>

                </select>

            </div>

            <div class="mb-3" id="yearSection">

                <label class="form-label">
                    Academic Year
                </label>

                <select
                    name="academic_year_id"
                    class="form-select">

                    <?php while($year = $years->fetch_assoc()){ ?>

                        <option value="<?php echo $year['id']; ?>">

                            <?php echo $year['year_name']; ?>

                        </option>

                    <?php } ?>

                </select>

                <small class="text-muted">
                    Required only for PYQs
                </small>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Semester
                </label>

                <select
                    name="semester"
                    class="form-select">

                    <?php for($i=1;$i<=8;$i++){ ?>

                        <option value="<?php echo $i; ?>">

                            Semester <?php echo $i; ?>

                        </option>

                    <?php } ?>

                </select>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Branch
                </label>

                <select
                    name="branch_id"
                    class="form-select">

                    <?php while($branch = $branches->fetch_assoc()){ ?>

                        <option value="<?php echo $branch['id']; ?>">

                            <?php echo $branch['branch_name']; ?>

                        </option>

                    <?php } ?>

                </select>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Subject
                </label>

                <select
                    name="subject_id"
                    class="form-select">

                    <?php while($subject = $subjects->fetch_assoc()){ ?>

                        <option value="<?php echo $subject['id']; ?>">

                            <?php echo $subject['subject_name']; ?>

                        </option>

                    <?php } ?>

                </select>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    PDF File
                </label>

                <input
                    type="file"
                    name="pdf_file"
                    class="form-control"
                    accept=".pdf"
                    required>

            </div>

            <button
                type="submit"
                class="btn btn-primary">

                Upload PDF

            </button>

        </form>

    </div>

</div>

</div>

<script>

const materialType =
document.querySelector(
'select[name="material_type"]'
);

const yearSection =
document.getElementById(
'yearSection'
);

function toggleYear(){

    if(
        materialType.value === "PYQ"
    ){

        yearSection.style.display =
        "block";

    }else{

        yearSection.style.display =
        "none";

    }
}

toggleYear();

materialType.addEventListener(
"change",
toggleYear
);

</script>

</body>
</html>
