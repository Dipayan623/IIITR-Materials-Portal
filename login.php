<?php

session_start();
require_once 'config/db.php';

if(isset($_SESSION['admin_id'])){
    header("Location: admin/dashboard.php");
    exit();
}

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $roll_number = trim($_POST['roll_number']);
    $password = $_POST['password'];

    $stmt = $conn->prepare(
        "SELECT * FROM admins WHERE roll_number = ?"
    );

    $stmt->bind_param("s", $roll_number);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows === 1){

        $admin = $result->fetch_assoc();

        if(password_verify(
            $password,
            $admin['password_hash']
        )){

            session_regenerate_id(true);

            $_SESSION['admin_id']
                = $admin['id'];

            $_SESSION['roll_number']
                = $admin['roll_number'];

            header("Location: admin/dashboard.php");
            exit();
        }
    }

    $error = "Invalid Roll Number or Password";
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/login.css">
<meta charset="UTF-8">
<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Admin Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
      rel="stylesheet">

<style>

body{
    background:#f8f9fa;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.login-card{
    width:100%;
    max-width:420px;
    border:none;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
}

.card-body{
    padding:35px;
}

.site-title{
    text-align:center;
    margin-bottom:25px;
}

</style>

</head>

<body>

<div class="card login-card">
<div class="card-body">

    <div class="site-title">

        <h2>IIITR Materials</h2>

        <p class="text-muted">
            Administrator Login
        </p>

    </div>

    <?php if(!empty($error)){ ?>

        <div class="alert alert-danger">

            <?php echo $error; ?>

        </div>

    <?php } ?>

    <form method="POST">

        <div class="mb-3">

            <label class="form-label">
                Roll Number
            </label>

            <input
                type="text"
                name="roll_number"
                class="form-control"
                required
            >

        </div>

        <div class="mb-3">

            <label class="form-label">
                Password
            </label>

            <div class="input-group">

                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control"
                    required
                >

                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    id="toggleBtn"
                    onclick="togglePassword()">

                    👁️

                </button>

            </div>

        </div>

        <button
            type="submit"
            class="btn btn-dark w-100">

            Login

        </button>

    </form>

    <div class="text-center mt-3">

        <a href="index.php">

            ← Back to Home

        </a>

    </div>

</div>

</div>

<script>

function togglePassword(){

    const password =
        document.getElementById("password");

    const btn =
        document.getElementById("toggleBtn");

    if(password.type === "password"){

        password.type = "text";
        btn.innerHTML = "🙈";

    }else{

        password.type = "password";
        btn.innerHTML = "👁️";

    }
}

</script>

<script src="assets/js/main.js"></script>
</body>
</html>