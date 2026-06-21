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

    $branch_name = trim($_POST['branch_name']);

    if(!empty($branch_name)){

        $check = $conn->prepare(
            "SELECT id FROM branches
             WHERE branch_name = ?"
        );

        $check->bind_param(
            "s",
            $branch_name
        );

        $check->execute();

        $result = $check->get_result();

        if($result->num_rows > 0){

            $error = "Branch already exists.";

        }else{

            $stmt = $conn->prepare(
                "INSERT INTO branches
                (branch_name)
                VALUES (?)"
            );

            $stmt->bind_param(
                "s",
                $branch_name
            );

            if($stmt->execute()){

                $message =
                    "Branch added successfully.";

            }else{

                $error =
                    "Failed to add branch.";
            }
        }
    }
}

$branches = $conn->query(
    "SELECT *
     FROM branches
     ORDER BY branch_name"
);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Add Branch</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
      rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Add Branch</h2>

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
                    Branch Name
                </label>

                <input
                    type="text"
                    name="branch_name"
                    class="form-control"
                    placeholder="Example: CSE"
                    required
                >

            </div>

            <button
                type="submit"
                class="btn btn-primary">

                Add Branch

            </button>

        </form>

    </div>

</div>

<div class="card">

    <div class="card-header">

        Existing Branches

    </div>

    <div class="card-body">

        <table class="table table-bordered">

            <thead>

            <tr>
                <th>ID</th>
                <th>Branch Name</th>
            </tr>

            </thead>

            <tbody>

            <?php while(
                $branch =
                $branches->fetch_assoc()
            ){ ?>

                <tr>

                    <td>

                        <?php
                        echo $branch['id'];
                        ?>

                    </td>

                    <td>

                        <?php
                        echo htmlspecialchars(
                            $branch['branch_name']
                        );
                        ?>

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