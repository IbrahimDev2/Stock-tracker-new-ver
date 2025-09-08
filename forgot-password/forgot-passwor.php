<?php
// Include database connection
include 'connection.php';

// Load Composer autoloader for PHPMailer
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize PHPMailer
$mail = new PHPMailer(true);
// Initialize variables
$email = '';
$error = '';
$key = '';
// Check if form is submitted and email is not empty
if(isset($_POST['email']) && (!empty($_POST['email']))){

    // Sanitize and validate email
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);

    // Initialize error variable if not set
    $error = '';

    if(!$email){
        $error .= "<p>Invalid email address. Please type a valid email address!</p>"; 
    } else {
        // Check if email exists in the database
        $sel_query = "SELECT * FROM `users` WHERE `st-email`='".$email."'";
        $result = mysqli_query($conn, $sel_query);

        // FIXED: Variable typo - should be $result, not $results
        $row = mysqli_num_rows($result);

        if($row == 0){  // Better to check 0 instead of ""
            $error .= "<p>No user is registered with this email address!</p>";
        }
    }
}

// If there is an error, show it and stop execution
if(!empty($error)){
    echo "<div class='error'>".$error."</div>
    <br> <a href='javascript:history.go(-1)'>Go Back</a>";
} else {
    // Generate expiration date for the reset link (1 day later)
    $expFormat = mktime(
        date("H"), date("i"), date("s"), date("m"), date("d")+1, date("Y")
    );
    $expDate = date("Y-m-d H:i:s",$expFormat);

    // Generate a unique reset key
    $key = md5((20148*2) . $email);
    $addKey = substr(md5(uniqid(rand(),1)),3,10); // FIXED: Correct variable name
    $key = $key . $addKey;

    // Insert temp reset data into password_reset_temp table
    mysqli_query($conn,
        "INSERT INTO `password_reset_temp` (`email`, `key`, `expDate`)
        VALUES ('".$email."', '".$key."', '".$expDate."');"
    );

    // Prepare the email body
    $output = '<p>Dear user,</p>';
    $output .= '<p>Please click on the following link to reset your password.</p>';
    $output .= '<p>-------------------------------------------------------------</p>';
    $output .= '<p><a href="https://www.allphptricks.com/forgot-password/reset-password.php?key='.$key.'&email='.$email.'&action=reset" target="_blank">
    https://www.allphptricks.com/forgot-password/reset-password.php?key='.$key.'&email='.$email.'&action=reset</a></p>';		
    $output .= '<p>-------------------------------------------------------------</p>';
    $output .= '<p>The link will expire after 1 day for security reason.</p>';
    $output .= '<p>If you did not request this, no action is needed.</p>';   	
    $output .= '<p>Thanks,</p>';
    $output .= '<p>AllPHPTricks Team</p>';

    $body = $output;
    $subject = "Password Recovery - AllPHPTricks.com";

    $email_to = $email;
    $fromserver = "info.ibrahim172@gmail.com"; 

    // Setup PHPMailer
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Host = "smtp.gmail.com"; // Your SMTP host
    $mail->SMTPAuth = true;
    $mail->Username = "noreply@yourwebsite.com"; // SMTP username
    $mail->Password = "password"; // SMTP password
    $mail->Port = 25; // Usually 587 for TLS
    $mail->IsHTML(true);
    $mail->From = "noreply@yourwebsite.com";
    $mail->FromName = "AllPHPTricks";
    $mail->Sender = $fromserver; // ReturnPath header
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AddAddress($email_to);

    // Send the email
    if(!$mail->Send()){
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
        echo "<div class='error'>
        <p>An email has been sent to you with instructions on how to reset your password.</p>
        </div><br /><br /><br />";
    }
}
?>   

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FORGOT PASSWORD</title>
    <style>
        .error p {
            color:#FF0000;
            font-size:20px;
            font-weight:bold;
            margin:50px;
        }
    </style>
</head>
<body>
    <form method="post" action="" name="reset"><br /><br />
        <label><strong>Enter Your Email Address:</strong></label><br /><br />
        <input type="email" name="email" placeholder="username@email.com" />
        <br /><br />
        <input type="submit" value="Reset Password"/>
    </form>
</body>
</html>
