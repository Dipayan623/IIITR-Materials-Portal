<?php

session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: ../login.php");
    exit();
}

require_once '../config/db.php';

$message = "";
$error = "";

/* ADD SUBJECT */

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $subject_name = trim($_POST['subject_name']);
    $semester = intval($_POST['semester']);
    $branch_id = intval($_POST['branch_id']);

    $check = $conn->prepare(
        "SELECT id
         FROM subjects
         WHERE subject_name = ?
         AND semester = ?
         AND branch_id = ?"
    );

    $check->bind_param(
        "sii",
        $subject_name,
        $semester,
        $branch_id
    );

    $check->execute();

    $result = $check->get_result();

    if($result->num_rows > 0){

        $error = "Subject already exists.";

    }else{

        $stmt = $conn->prepare(
            "INSERT INTO subjects
            (subject_name, semester, branch_id)
            VALUES (?, ?, ?)"
        );

        $stmt->bind_param(
            "sii",
            $subject_name,
            $semester,
            $branch_id
        );

        if($stmt->execute()){

            $message =
            "Subject added successfully.";

        }else{

            $error =
            "Failed to add subject.";
        }
    }
}

/* LOAD BRANCHES */

$branches = $conn->query(
    "SELECT *
     FROM branches
     ORDER BY branch_name"
);

/* LOAD SUBJECTS */

$subjects = $conn->query(
    "SELECT
        subjects.id,
        subjects.subject_name,
        subjects.semester,
        branches.branch_name

     FROM subjects

     JOIN branches
     ON subjects.branch_id = branches.id

     ORDER BY subjects.semester"
);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Add Subject</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
      rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">
<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Add Subject</h2>

    <a href="./dashboard.php"
       class="btn btn-secondary">

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

<div class="card mb-4">

    <div class="card-body">

        <form method="POST">

            <div class="mb-3">

                <label class="form-label">
                    Subject Name
                </label>

                <input
                    type="text"
                    name="subject_name"
                    class="form-control"
                    placeholder="Data Structures"
                    required
                >

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Semester
                </label>

                <select
                    name="semester"
                    class="form-select"
                    required>

                    <option value="">
                        Select Semester
                    </option>

                    <?php
                    for($i=1;$i<=8;$i++){
                    ?>

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
                    class="form-select"
                    required>

                    <option value="">
                        Select Branch
                    </option>

                    <?php while(
                        $branch =
                        $branches->fetch_assoc()
                    ){ ?>

                        <option
                        value="<?php echo $branch['id']; ?>">

                            <?php
                            echo $branch['branch_name'];
                            ?>

                        </option>

                    <?php } ?>

                </select>

            </div>

            <button
                type="submit"
                class="btn btn-primary">

                Add Subject

            </button>

        </form>

    </div>

</div>

<div class="card">

    <div class="card-header">

        Existing Subjects

    </div>

    <div class="card-body">

        <table class="table table-bordered">

            <thead>

            <tr>

                <th>ID</th>
                <th>Subject</th>
                <th>Semester</th>
                <th>Branch</th>

            </tr>

            </thead>

            <tbody>

            <?php while(
                $subject =
                $subjects->fetch_assoc()
            ){ ?>

                <tr>

                    <td>
                        <?php echo $subject['id']; ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars(
                            $subject['subject_name']
                        ); ?>
                    </td>

                    <td>
                        <?php echo $subject['semester']; ?>
                    </td>

                    <td>
                        <?php echo $subject['branch_name']; ?>
                    </td>

                </tr>

            <?php } ?>

            </tbody>

        </table>

    </div>

</div>
</div>

<script src="../assets/js/admin.js"></script>
</body>
</html>