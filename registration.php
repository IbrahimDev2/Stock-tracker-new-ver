<?php
/**
* User Registration Page
*
* This script handles user registration for the Stock Tracker application.
*
* Features:
* - Prevents direct access by defining 'APP_INIT'.
* - Connects to the database via 'connection.php'.
* - Redirects logged-in users to the dashboard.
* - Processes registration form submission:
* - Sanitizes and validates user input (full name, address, email, city, username, password, gender, agreement to terms).
* - Checks for existing username or email in the database.
* - Validates password match and agreement to terms.
* - Hashes the password using PASSWORD_DEFAULT.
* - Inserts new user into the 'users' table if validation passes.
* - Displays success or error messages accordingly.
* - Renders a registration form with Bootstrap styling.
* - Includes password visibility toggle functionality via JavaScript.
*
* Variables:
* - $errors: Array to store validation and database errors.
* - $success: String to store success message.
*
* Security:
* - Uses mysqli_real_escape_string to prevent SQL injection.
* - Passwords are securely hashed before storage.
*
* @package StockTracker
* @author [Ibrahim Khalil]
* @version 1.0
*/
// session start krdo
session_start();
// Prevent direct access
// constant define kro jo file ko access  krne se rokega
define('APP_INIT', true);
// connection file add kro
include 'connection.php';

// ikhali variable bnao jiska naam error h  or usko array bna do
$errors = [];
// IK OR KHALI VARAIBLE BNAO JISKA NAME SUCCESS H OR WO KHALI H
$success = '';
// AGR SESSION KE USER_iD KEY EXSIST KRTI H TO IS BLOCK KO RUN KRDO AND AGR NI EXSIST KRTI TO IS BLOCK KO SKIP KRDO
if (isset($_SESSION['email'])) {
    // YH FUNCTION KEH RAHA KE BROWSER PE HTTP REQUEST REDIRECT KRNI H OR LOCATION SET KRDI H
    header("Location: dashboard.php");
   //YH FUNCTION CODE KO TERMINATE BND KREGA IS BLOCK AB OR LINE RUN NI HOGI ISKE BAD BAHIR KA CODE EXECUTE HOGA
    exit();

// IDHR AKR IF KA BLOCK END HOJAEGA AB AND AGR TO KEY EXSIST NI KRTI TO  YH BLOCK SKIP HOJAEGA
}
if (isset($_POST['submit-btn'])) {


    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $address  = mysqli_real_escape_string($conn, $_POST['address']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $city     = mysqli_real_escape_string($conn, $_POST['city']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = trim($_POST['password'] ?? '');
    $retype_pass = trim($_POST['retypepassword'] ?? '');
    $gender   = $_POST['gender'] ?? null;
    $agree    = isset($_POST['atc']) ? 1 : 0;


    if ($password !== $retype_pass) {
        $errors['password'] = "Passwords do not match.";
    }


    $user_check_query = "SELECT * FROM users WHERE `st-username`='$username' OR `st-email`='$email' LIMIT 1";
    $result = mysqli_query($conn, $user_check_query);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        if ($user['st-username'] === $username) {
            $errors['username'] = "Username already exists.";
        }
        if ($user['st-email'] === $email) {
            $errors['email'] = "Email already exists.";
        }
    }


    if ($agree !== 1) {
        $errors['agree'] = "Please agree to the terms.";
    }


    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert_query = "INSERT INTO users 
            (`st-full-name`, `st-address`, `st-email`, `st-city`, `st-gender`, `st-username`, `st-password`) 
            VALUES('$fullname', '$address', '$email', '$city', '$gender', '$username', '$hashed_password')";

        if (mysqli_query($conn, $insert_query)) {
            $success = "User Registered Successfully!";
            $_SESSION['success'] = $success;

            header("Location: /Stock-tracker-new-ver/index.php");
            exit();

            exit();
        } else {
            $errors['general'] = "Database error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>


    <link href="css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap/bootstrap-reset.css" rel="stylesheet">

    <link href="css/style.css" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet" />
    <style>

    </style>
</head>

<body class="registration-body">
    <div class="container">

        <form class="form-signin" action="" method="POST">
            <h2 class="form-signin-heading">Register Now</h2>


            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>


            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-danger"><?= $errors['general'] ?></div>
            <?php endif; ?>

            <div class="login-wrap">
                <p>Enter your personal details below</p>


                <input type="text" name="fullname" id="fullname" class="form-control"
                    placeholder="Full Name" value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>">
                <?php if (isset($errors['fullname'])): ?>
                    <div class="alert alert-danger"><?= $errors['fullname'] ?></div>
                <?php endif; ?>


                <input type="text" name="address" id="address" class="form-control"
                    placeholder="Address" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">


                <input type="text" name="email" id="email" class="form-control"
                    placeholder="Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                <?php if (isset($errors['email'])): ?>
                    <div class="alert alert-danger"><?= $errors['email'] ?></div>
                <?php endif; ?>


                <input type="text" name="city" id="city" class="form-control"
                    placeholder="City/Town" value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">


                <div class="radios">
                    <label class="col-lg-6 col-sm-6" for="male">
                        <input name="gender" id="male" value="male" type="radio"
                            <?= (($_POST['gender'] ?? 'male') === 'male') ? 'checked' : '' ?> /> Male
                    </label>
                    <label class="col-lg-6 col-sm-6" for="female">
                        <input name="gender" id="female" value="female" type="radio"
                            <?= (($_POST['gender'] ?? '') === 'female') ? 'checked' : '' ?> /> Female
                    </label>
                </div>

                <p>Enter your account details below</p>

                <input type="text" name="username" id="username" class="form-control"
                    placeholder="User Name" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                <?php if (isset($errors['username'])): ?>
                    <div class="alert alert-danger"><?= $errors['username'] ?></div>
                <?php endif; ?>


                <div class="password-container">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter password">
                    <button type="button" id="togglePass" onclick="togglePassword('password','togglePass')">👁️</button>

                </div>


                <div class="password-container">
                    <input type="password" name="retypepassword" id="retypepassword" class="form-control" placeholder="Re-type Password">
                    <button type="button" id="toggleRetype" onclick="togglePassword('retypepassword','toggleRetype')">👁️</button>

                </div>
                <?php if (isset($errors['password'])): ?>
                    <div class="alert alert-danger"><?= $errors['password'] ?></div>
                <?php endif; ?>


                <label class="checkbox">
                    <input type="checkbox" name="atc" id="atc" value="1" <?= isset($_POST['atc']) ? 'checked' : '' ?>>
                    I agree to the Terms of Service and Privacy Policy
                </label>
                <?php if (isset($errors['agree'])): ?>
                    <div class="alert alert-warning"><?= $errors['agree'] ?></div>
                <?php endif; ?>

                <button name="submit-btn" id="submit-btn" class="btn btn-lg btn-login btn-block" type="submit">Submit</button>

                <div class="registration">
                    Already Registered?
                    <a href="index.php">Login</a>
                </div>
            </div>
        </form>
    </div>

    <script>
        function togglePassword(fieldId, btnId) {
            let passField = document.getElementById(fieldId);
            let btn = document.getElementById(btnId);

            if (passField.type === "password") {
                passField.type = "text";
                btn.textContent = "🙈";
            } else {
                passField.type = "password";
                btn.textContent = "👁️";
            }
        }
    </script>
</body>

</html>