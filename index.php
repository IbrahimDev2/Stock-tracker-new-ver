<?php
session_start();

include 'connection.php'; // DB connection

$errors = [];

if (isset($_POST['submit-login'])) {

    // 1️⃣ Get form data
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $rememberme = isset($_POST['rem-me']) ? $_POST['rem-me'] : '';

    // 2️⃣ Fetch user by email only (password not in SQL!)
    $qry = "SELECT * FROM users WHERE `st-email` = '$email' LIMIT 1";
    $result = mysqli_query($conn, $qry);

    if ($result && mysqli_num_rows($result) > 0) {
        // 3️⃣ User exists
        $row = mysqli_fetch_assoc($result);

        // 4️⃣ Verify password using PHP's password_verify
        if (password_verify($password, $row['st-password'])) {

            // 5️⃣ Set session
            $_SESSION['email'] = $row['st-email'];
            $_SESSION['success'] = "Login Successful";

            // 6️⃣ Optional: handle "Remember Me" cookie (if needed)
            if ($rememberme) {
                setcookie('user_email', $row['st-email'], time() + (86400 * 30), "/"); // 30 days
            }

            // 7️⃣ Redirect to dashboard
            header("Location: dashboard.php");
            exit;

        } else {
            // Password does not match
            $errors['password'] = "Password does not match";
        }

    } else {
        // User with this email not found
        $errors['user'] = "User not found";
    }
     // Process complete → clear POST
    $_POST = array(); // ya unset($_POST['name']); unset($_POST['email']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <!-- Bootstrap CSS -->
    <link href="css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap/bootstrap-reset.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <!-- Custom styles -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet" />
</head>
<body class="login-body">

<div class="container overlay-container"> <!-- Main overlay div -->

    <!-- Display success message if login was successful -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible" 
             role="alert">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Display errors if any -->
    <?php if (isset($errors['user'])): ?>
        <div class="alert alert-danger"><?= $errors['user'] ?></div>
    <?php endif; ?>
    <?php if (isset($errors['password'])): ?>
        <div class="alert alert-danger"><?= $errors['password'] ?></div>
    <?php endif; ?>

    <!-- Login Form -->
    <form class="form-signin" action="" method="POST">
        <h2 class="form-signin-heading">Sign in now</h2>
        <div class="login-wrap">
            
            <!-- Email input -->
            <div class="form-group">
                <input type="email" name="email" id="email" class="form-control" placeholder="User Email" autofocus required>
            </div>
            
            <!-- Password input with toggle visibility -->
            <div class="form-group password-container">
                <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                <button type="button" id="toggle_pass" onclick="togglepass('password','toggle_pass')">👁️</button>
            </div>
            
            <!-- Remember me and forgot password -->
            <label class="checkbox">
                <input type="checkbox" name="rem-me" id="rem-me" value="1"> Remember me
                <span class="pull-right">
                    <a data-toggle="modal" href="index.php#myModal"> Forgot Password?</a>
                </span>
            </label>
            
            <!-- Submit button -->
            <button class="btn btn-lg btn-login btn-block" name="submit-login" type="submit">Sign in</button>
            
            <!-- Registration link -->
            <div class="registration">
                Don't have an account yet?
                <a href="registration.php">Create an account</a>
            </div>
        </div>
    </form>

</div> <!-- End of overlay-container -->

<!-- Toggle password visibility -->
<script>
function togglepass(passwordFieldId, toggleBtnId){
    let passField = document.getElementById(passwordFieldId);
    let btn = document.getElementById(toggleBtnId);

    if (passField.type === "password") {
        passField.type = "text";
        btn.textContent = "🙈"; // Closed eye
    } else {
        passField.type = "password";
        btn.textContent = "👁️"; // Open eye
    }
}
</script>

<!-- jQuery and Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

</body>
</html>
