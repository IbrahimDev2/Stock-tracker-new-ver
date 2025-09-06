<?php
session_start();
include 'connection.php';

$errors = [];
$success = '';

if(isset($_POST['submit-btn'])) {

    // Sanitize inputs
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $address  = mysqli_real_escape_string($conn, $_POST['address']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $city     = mysqli_real_escape_string($conn, $_POST['city']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = trim($_POST['password'] ?? '');
    $retype_pass = trim($_POST['retypepassword'] ?? '');
    $gender   = $_POST['gender'] ?? null;
    $agree    = isset($_POST['atc']) ? 1 : 0;

    // Validate password match
    if($password !== $retype_pass){
        $errors['password'] = "Passwords do not match.";
    }

    // Check if username or email already exists
    $user_check_query = "SELECT * FROM users WHERE `st-username`='$username' OR `st-email`='$email' LIMIT 1";
    $result = mysqli_query($conn, $user_check_query);
    $user = mysqli_fetch_assoc($result);

    if($user){
        if($user['st-username'] === $username){
            $errors['username'] = "Username already exists.";
        }
        if($user['st-email'] === $email){
            $errors['email'] = "Email already exists.";
        }
    }

    // Terms and conditions
    if($agree !== 1){
        $errors['agree'] = "Please agree to the terms.";
    }

    // If no errors -> insert user
    if(empty($errors)){
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert_query = "INSERT INTO users 
            (`st-full-name`, `st-address`, `st-email`, `st-city`, `st-gender`, `st-username`, `st-password`) 
            VALUES('$fullname', '$address', '$email', '$city', '$gender', '$username', '$hashed_password')";

        if(mysqli_query($conn, $insert_query)){
           $success = "User Registered Successfully!";
            $_SESSION['success'] = $success; // yahan $success ko session me dal diya

             header("Location: /Stock-tracker-new-ver/index.php"); // root se correct path
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

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap/bootstrap-reset.css" rel="stylesheet">
    <!-- Custom styles -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet" />
    <style>

</style>
</head>

<body class="registration-body">
<div class="container">

  <form class="form-signin" action="" method="POST">
    <h2 class="form-signin-heading">Register Now</h2>

    <!-- Success Message -->
    <?php if(!empty($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <!-- General DB Error -->
    <?php if(isset($errors['general'])): ?>
        <div class="alert alert-danger"><?= $errors['general'] ?></div>
    <?php endif; ?>

    <div class="login-wrap">
        <p>Enter your personal details below</p>

        <!-- Full Name -->
        <input type="text" name="fullname" id="fullname" class="form-control" 
               placeholder="Full Name" value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>">
        <?php if(isset($errors['fullname'])): ?>
            <div class="alert alert-danger"><?= $errors['fullname'] ?></div>
        <?php endif; ?>

        <!-- Address -->
        <input type="text" name="address" id="address" class="form-control" 
               placeholder="Address" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">

        <!-- Email -->
        <input type="text" name="email" id="email" class="form-control" 
               placeholder="Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        <?php if(isset($errors['email'])): ?>
            <div class="alert alert-danger"><?= $errors['email'] ?></div>
        <?php endif; ?>

        <!-- City -->
        <input type="text" name="city" id="city" class="form-control" 
               placeholder="City/Town" value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">

        <!-- Gender -->
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

        <!-- Username -->
        <input type="text" name="username" id="username" class="form-control" 
               placeholder="User Name" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        <?php if(isset($errors['username'])): ?>
            <div class="alert alert-danger"><?= $errors['username'] ?></div>
        <?php endif; ?>

        <!-- Password -->
       <div class="password-container">
  <input type="password" name="password" id="password" class="form-control" placeholder="Enter password">
  <button type="button" id="togglePass" onclick="togglePassword('password','togglePass')">👁️</button>

</div>

        <!-- Retype Password -->
         <div class="password-container">
        <input type="password" name="retypepassword" id="retypepassword" class="form-control" placeholder="Re-type Password">
        <button type="button" id="toggleRetype" onclick="togglePassword('retypepassword','toggleRetype')">👁️</button>

</div>
        <?php if(isset($errors['password'])): ?>
            <div class="alert alert-danger"><?= $errors['password'] ?></div>
        <?php endif; ?>

        <!-- Terms -->
        <label class="checkbox">
            <input type="checkbox" name="atc" id="atc" value="1" <?= isset($_POST['atc']) ? 'checked' : '' ?>> 
            I agree to the Terms of Service and Privacy Policy
        </label>
        <?php if(isset($errors['agree'])): ?>
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
    btn.textContent = "🙈"; // closed eye
  } else {
    passField.type = "password";
    btn.textContent = "👁️"; // open eye
  }
}
</script>
</body>
</html>
