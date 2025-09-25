<?php
if (!defined('APP_INIT')) {
define('APP_INIT', true);
}
include('connection.php'); // Database connection
$error = '';
$success = '';

// 1️⃣ Check if user clicked reset link
if (isset($_GET["key"]) && isset($_GET["email"]) && isset($_GET["action"]) 
    && ($_GET["action"]=="reset") && !isset($_POST["action"])) {

    $key = $_GET["key"];
    $email = $_GET["email"];
    $curDate = date("Y-m-d H:i:s");

    $query = mysqli_query($conn,
        "SELECT * FROM `password_reset_temp` WHERE `key`='".$key."' AND `email`='".$email."';"
    );

    $row = mysqli_num_rows($query);

    if ($row == 0) {
        $error = '<strong>Invalid Link</strong><br>The link is invalid or expired. <a href="forgot-passwor.php">Click here</a> to request a new one.';
    } else {
        $row = mysqli_fetch_assoc($query);
        $expDate = $row['expDate'];

        if ($expDate < $curDate) {
            $error = '<strong>Link Expired</strong><br>The link has expired. <a href="forgot-passwor.php">Click here</a> to request a new one.';
        }
    }
}

// 2️⃣ Handle form submission
if (isset($_POST["email"]) && isset($_POST["action"]) && $_POST["action"]=="update") {
    $pass1 = mysqli_real_escape_string($conn, $_POST["pass1"]);
    $pass2 = mysqli_real_escape_string($conn, $_POST["pass2"]);
    $email = $_POST["email"];

    if ($pass1 != $pass2) {
        $error = "Passwords do not match.";
    } else {
        $pass1 = password_hash($pass1, PASSWORD_DEFAULT);

        mysqli_query($conn,
            "UPDATE `users` SET `st-password`='".$pass1."' WHERE `st-email`='".$email."';"
        );

        mysqli_query($conn, "DELETE FROM `password_reset_temp` WHERE `email`='".$email."';");

        $success = "Congratulations! Your password has been updated successfully. <a href='../index.php'>Login here</a>.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reset Password</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background-color: #f7f7f7;
}
.reset-panel {
    max-width: 450px;
    margin: 50px auto;
}
.panel-heading {
    font-size: 18px;
    font-weight: bold;
    text-align: center;
}
.panel-success{
        background: #0E402D;
    color: #fff;
}
</style>
</head>
<body>

<div class="container">
    <div class="panel panel-success reset-panel">
        <div class="panel-heading">Reset Your Password</div>
        <div class="panel-body">

            <!-- Show error -->
            <?php if ($error != ''): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Show success -->
            <?php if ($success != ''): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <!-- Show form only if no success -->
            <?php if ($success == '' && $error == '' || (isset($expDate) && $expDate >= $curDate)): ?>
            <form method="post" action="">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">

                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="pass1" class="form-control" placeholder="Enter new password" required>
                </div>

                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="pass2" class="form-control" placeholder="Confirm password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
            </form>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
