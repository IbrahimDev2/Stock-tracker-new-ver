<?php
session_start();
define('APP_INIT', true);

include 'connection.php'; // DB connection

$errors = [];

if (isset($_POST['submit-login'])) {

    // Sanitize and retrieve login form data
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $rememberme = isset($_POST['rem-me']) ? $_POST['rem-me'] : '';

    // Fetch user record by email from database
    $qry = "SELECT * FROM users WHERE `st-email` = '$email' LIMIT 1";
    $result = mysqli_query($conn, $qry);

    if ($result && mysqli_num_rows($result) > 0) {
        // Check if user exists in database
        $row = mysqli_fetch_assoc($result);

        // Verify submitted password against hashed password
        if (password_verify($password, $row['st-password'])) {

            // Store user email and success message in session
            $_SESSION['email'] = $row['st-email'];
            $_SESSION['success'] = "Login Successful";

            // Set "Remember Me" cookie for persistent login if checked
            if ($rememberme) {
                setcookie('user_email', $row['st-email'], time() + (86400 * 30), "/"); // 30 days
            }

            // Redirect authenticated user to dashboard
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
    // Clear POST data after processing to prevent resubmission
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
    <style>
        .password-container {
            position: relative;
        }

        .password-container button {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            font-size: 16px;
            cursor: pointer;
        }

        .modal-header {
            background: #0E402D;
            color: #fff;
        }

        .modal-footer .btn-login {
            background: #0E402D;
            color: #fff;
        }
    </style>
</head>

<body class="login-body">

    <div class="container overlay-container">

        <!-- Display success message if login was successful -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible" role="alert">
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
                <div class="form-group">
                    <input type="email" name="email" id="email" class="form-control" placeholder="User Email" autofocus required>
                </div>
                <div class="form-group password-container">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                    <button type="button" id="toggle_pass" onclick="togglepass('password','toggle_pass')">👁️</button>
                </div>
                <label class="checkbox">
                    <input type="checkbox" name="rem-me" id="rem-me" value="1"> Remember me
                    <span class="pull-right">
                        <a data-toggle="modal" href="#myModal"> Forgot Password?</a>
                    </span>
                </label>
                <button class="btn btn-lg btn-login btn-block" name="submit-login" type="submit">Sign in</button>
                <div class="registration">
                    Don't have an account yet? <a href="registration.php">Create an account</a>
                </div>
            </div>
        </form>

    </div>

    <!-- Forgot Password Modal -->
    <form method="post" action="forgot-password/forgot-passwor.php" name="reset">
        <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Forgot Password ?</h4>
                    </div>
                    <div class="modal-body">
                        <p>Enter your e-mail address below to reset your password.</p>
                        <input type="text" name="email" placeholder="Email" autocomplete="off" class="form-control placeholder-no-fix">
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-default" type="button">Cancel</button>
                        <button class="btn btn-success btn-login" type="submit">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Email Sent Modal -->
    <?php
    if (isset($_GET['msg']) && $_GET['msg'] == 'email_sent') {
        echo "
    <div class='modal fade' id='forgotPasswordModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel'>
      <div class='modal-dialog'>
        <div class='modal-content'>
          <div class='modal-header'>
            <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
            <h4 class='modal-title' id='myModalLabel'>Forgot Password</h4>
          </div>
          <div class='modal-body'>
            An email has been sent to you with instructions on how to reset your password.
          </div>
          <div class='modal-footer'>
            <button type='button' class='btn btn-primary' data-dismiss='modal' id='closeEmailModal'>OK</button>
          </div>
        </div>
      </div>
    </div>
    ";
    }
    ?>

    <!-- jQuery (required for Bootstrap 3 JS) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

    <!-- JavaScript to toggle password field visibility -->
    <script>
        function togglepass(passwordFieldId, toggleBtnId) {
            let passField = document.getElementById(passwordFieldId);
            let btn = document.getElementById(toggleBtnId);

            if (passField.type === "password") {
                passField.type = "text";
                btn.textContent = "🙈";
            } else {
                passField.type = "password";
                btn.textContent = "👁️";
            }
        }
    </script>

    <!-- Show email sent modal -->
    <script>
        $(document).ready(function() {
            // Display confirmation modal if password reset email was sent
            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('msg') === 'email_sent') {
                $('#forgotPasswordModal').modal('show');
            }

            // Clean URL query string after modal is closed
            $('#closeEmailModal').click(function() {
                $('#forgotPasswordModal').modal('hide');
                // Remove the query string without reloading
                if (history.pushState) {
                    var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                    window.history.pushState({
                        path: newUrl
                    }, '', newUrl);
                }
            });
        });
    </script>


</body>

</html>