<?php
/**
* Entry point for the Stock Tracker application.
*
* This file initializes the application and handles incoming requests.
*
* @package StockTracker
*/
session_start();
define('APP_INIT', true);

include 'connection.php';

$errors = [];

if (isset($_POST['submit-login'])) {


    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $rememberme = isset($_POST['rem-me']) ? $_POST['rem-me'] : '';


    $qry = "SELECT * FROM users WHERE `st-email` = '$email' LIMIT 1";
    $result = mysqli_query($conn, $qry);

    if ($result && mysqli_num_rows($result) > 0) {

        $row = mysqli_fetch_assoc($result);


        if (password_verify($password, $row['st-password'])) {

            $_SESSION['email'] = $row['st-email'];
            $_SESSION['success'] = "Login Successful";


            if ($rememberme) {
                setcookie('user_email', $row['st-email'], time() + (86400 * 30), "/"); // 30 days
            }


            header("Location: dashboard.php");
            exit;
        } else {

            $errors['password'] = "Password does not match";
        }
    } else {

        $errors['user'] = "User not found";
    }

    $_POST = array();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>

    <link href="css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap/bootstrap-reset.css" rel="stylesheet">

    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />

    <link href="css/style.css" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet" />
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


        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible" role="alert">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($errors['user'])): ?>
            <div class="alert alert-danger"><?= $errors['user'] ?></div>
        <?php endif; ?>
        <?php if (isset($errors['password'])): ?>
            <div class="alert alert-danger"><?= $errors['password'] ?></div>
        <?php endif; ?>

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


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>


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


    <script>
        $(document).ready(function() {

            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('msg') === 'email_sent') {
                $('#forgotPasswordModal').modal('show');
            }


            $('#closeEmailModal').click(function() {
                $('#forgotPasswordModal').modal('hide');

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