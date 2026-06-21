<?php
session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: ../login.php");
    exit();
}

require_once '../config/db.php';

$message = "";
$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $year_name = trim($_POST['year_name']);

    if(!empty($year_name)){

        $check = $conn->prepare(
            "SELECT id FROM academic_years WHERE year_name = ?"
        );

        $check->bind_param("s", $year_name);
        $check->execute();

        $result = $check->get_result();

        if($result->num_rows > 0){

            $error = "Academic Year already exists.";

        }else{

            $stmt = $conn->prepare(
                "INSERT INTO academic_years (year_name)
                 VALUES (?)"
            );

            $stmt->bind_param("s", $year_name);

            if($stmt->execute()){
                $message = "Academic Year added successfully.";
            }else{
                $error = "Failed to add Academic Year.";
            }
        }
    }
}

$years = $conn->query(
    "SELECT * FROM academic_years
     ORDER BY year_name DESC"
);
?>

<!DOCTYPE html>

<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Add Academic Year</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Add Academic Year</h2>

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
                    Academic Year
                </label>

                <input
                    type="text"
                    name="year_name"
                    class="form-control"
                    placeholder="Example: 2025-26"
                    required
                >

            </div>

            <button
                type="submit"
                class="btn btn-primary">

                Add Year

            </button>

        </form>

    </div>

</div>

<div class="card">

    <div class="card-header">
        Existing Academic Years
    </div>

    <div class="card-body">

        <table class="table table-bordered">

            <thead>

            <tr>
                <th>ID</th>
                <th>Academic Year</th>
            </tr>

            </thead>

            <tbody>

            <?php while($year = $years->fetch_assoc()){ ?>

                <tr>

                    <td>
                        <?php echo $year['id']; ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($year['year_name']); ?>
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