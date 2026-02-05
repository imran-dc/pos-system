<?php

session_start();
require_once "config/db.php";

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if(empty($username) || empty($password)){
        $error = "Please enter username and password.";
    }else{

        $stmt = $imwp_conn->prepare("SELECT * FROM imwp_users WHERE username=? AND status='active' LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows == 1){
            $user = $result->fetch_assoc();

            if(password_verify($password, $user["password"])){
                $_SESSION["imwp_user_id"] = $user["id"];
                $_SESSION["imwp_username"] = $user["username"];
                $_SESSION["imwp_role"] = $user["role"];

                header("Location: dashboard.php");
                exit;
            }else{
                $error = "Password wrong.";
            }

        }else{
            $error = "User not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - IMWP POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-4">

<div class="card p-4 shadow">

<h4 class="mb-3 text-center">IMWP POS Login</h4>

<?php if($error): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST">

<div class="mb-3">
<label>Username</label>
<input type="text" name="username" class="form-control" required>
</div>

<div class="mb-3">
<label>Password</label>
<input type="password" name="password" class="form-control" required>
</div>

<button type="submit" class="btn btn-dark w-100">Login</button>

</form>

</div>

</div>
</div>
</div>

</body>
</html>
