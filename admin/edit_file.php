<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: manage_files.php");
    exit();
}

$id = intval($_GET['id']);

/* LOAD MATERIAL */

$stmt = $conn->prepare(
    "SELECT * FROM materials WHERE id=?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: manage_files.php");
    exit();
}

$material = $result->fetch_assoc();

/* LOAD DROPDOWNS */

$years = $conn->query(
    "SELECT * FROM academic_years ORDER BY year_name"
);

$branches = $conn->query(
    "SELECT * FROM branches ORDER BY branch_name"
);

$subjects = $conn->query(
    "SELECT * FROM subjects ORDER BY subject_name"
);

/* UPDATE */

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title']);
    $material_type = $_POST['material_type'];$academic_year_id = null;
    if($material_type === "PYQ"){
        $academic_year_id =
        intval($_POST['academic_year_id']);
    }
    $semester = intval($_POST['semester']);
    $branch_id = intval($_POST['branch_id']);
    $subject_id = intval($_POST['subject_id']);

    $file_name = $material['file_name'];
    $file_path = $material['file_path'];

    /* NEW FILE UPLOAD */

    if (
    isset($_FILES['pdf']) &&
    $_FILES['pdf']['error'] === 0
) {

    $extension = strtolower(
        pathinfo(
            $_FILES['pdf']['name'],
            PATHINFO_EXTENSION
        )
    );
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $_FILES['pdf']['tmp_name']);
    finfo_close($finfo);

    if(
        $extension !== "pdf" ||
        $mimeType !== "application/pdf"
    ){
        $message = "Only valid PDF files are allowed.";
    } else {

        if(
            file_exists(
                "../" .
                $material['file_path']
            )
        ){
            unlink(
                "../" .
                $material['file_path']
            );
        }

        $newFileName =
            bin2hex(random_bytes(8)) . "_" .
            preg_replace(
                "/[^a-zA-Z0-9._-]/",
                "",
                $_FILES['pdf']['name']
            );

        $uploadPath =
            "../uploads/" .
            $newFileName;

        move_uploaded_file(
            $_FILES['pdf']['tmp_name'],
            $uploadPath
        );

        $file_name = $newFileName;
        $file_path = "uploads/" . $newFileName;
    }
}

    $update = $conn->prepare(

        "UPDATE materials

        SET

        title=?,
        material_type=?,
        academic_year_id=?,
        semester=?,
        branch_id=?,
        subject_id=?,
        file_name=?,
        file_path=?

        WHERE id=?"

    );

    $update->bind_param(

        "ssiiiissi",

        $title,
        $material_type,
        $academic_year_id,
        $semester,
        $branch_id,
        $subject_id,
        $file_name,
        $file_path,
        $id

    );

    if ($update->execute()) {

        $message = "Material updated successfully.";

        header(
            "Refresh:1; url=manage_files.php"
        );
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <style>

    #yearSection{
        display:none;
    }

    </style>
<meta charset="UTF-8">
<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title>Edit Material</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
      rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="card shadow">

<div class="card-body">

<h2 class="mb-4">

Edit Material

</h2>

<?php if($message): ?>

<div class="alert alert-success">

<?php echo $message; ?>

</div>

<?php endif; ?>

<form method="POST"
      enctype="multipart/form-data">

<div class="mb-3">

<label class="form-label">

Title

</label>

<input
type="text"
name="title"
class="form-control"
value="<?php echo htmlspecialchars($material['title']); ?>"
required>

</div>

<div class="mb-3">

<label class="form-label">

Material Type

</label>

<select
name="material_type"
class="form-select">

<?php

$types = [
"Notes",
"Book",
"Assignment",
"Lab",
"PYQ"
];


foreach($types as $type){

$selected =
($material['material_type'] == $type)
? "selected"
: "";

echo
"<option value='$type' $selected>
$type
</option>";
}

?>

</select>

</div>

<div class="mb-3" id="yearSection">

<label class="form-label">

Academic Year

</label>

<select
name="academic_year_id"
class="form-select">

<?php while($year = $years->fetch_assoc()): ?>

<option
value="<?php echo $year['id']; ?>"

<?php
if(
$year['id']
==
$material['academic_year_id']
){
echo "selected";
}
?>

>

<?php echo $year['year_name']; ?>

</option>

<?php endwhile; ?>

</select>

<small class="text-muted">

Only required for PYQs

</small>

</div>

<div class="mb-3">

<label class="form-label">

Semester

</label>

<select
name="semester"
class="form-select">

<?php for($i=1;$i<=8;$i++): ?>

<option
value="<?php echo $i; ?>"

<?php
if(
$i ==
$material['semester']
)
echo "selected";
?>

>

Semester <?php echo $i; ?>

</option>

<?php endfor; ?>

</select>

</div>

<div class="mb-3">

<label class="form-label">

Branch

</label>

<select
name="branch_id"
class="form-select">

<?php while($branch = $branches->fetch_assoc()): ?>

<option
value="<?php echo $branch['id']; ?>"

<?php
if(
$branch['id']
==
$material['branch_id']
)
echo "selected";
?>

>

<?php
echo $branch['branch_name'];
?>

</option>

<?php endwhile; ?>

</select>

</div>

<div class="mb-3">

<label class="form-label">

Subject

</label>

<select
name="subject_id"
class="form-select">

<?php while($subject = $subjects->fetch_assoc()): ?>

<option
value="<?php echo $subject['id']; ?>"

<?php
if(
$subject['id']
==
$material['subject_id']
)
echo "selected";
?>

>

<?php
echo $subject['subject_name'];
?>

</option>

<?php endwhile; ?>

</select>

</div>

<div class="mb-3">

<label class="form-label">

Current File

</label>

<input
type="text"
class="form-control"
value="<?php echo $material['file_name']; ?>"
readonly>

</div>

<div class="mb-3">

<label class="form-label">

Replace PDF (Optional)

</label>

<input
type="file"
name="pdf"
class="form-control"
accept=".pdf">

</div>

<button
type="submit"
class="btn btn-success">

Update Material

</button>

<a href="manage_files.php"
   class="btn btn-secondary">

Back

</a>

</form>

</div>

</div>

</div>

<script src="../assets/js/admin.js"></script>
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
        materialType.value ===
        "PYQ"
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