<?php
// ============================================
// FORGOT PASSWORD SCRIPT USING PHPMailer
// ============================================
if (!defined('APP_INIT')) {
define('APP_INIT', true);
}
// 1️⃣ Include database connection
include 'connection.php'; // Make sure 'connection.php' has your $conn variable for DB

// 2️⃣ Load Composer's autoloader for PHPMailer
require __DIR__ . '/../vendor/autoload.php'; // Adjust path if PHPMailer is elsewhere

// 3️⃣ Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 4️⃣ Initialize variables
$email = ''; // Stores the email entered by the user
$error = ''; // Stores error messages
$key = '';   // Will store unique password reset key

// 5️⃣ Check if the form is submitted and email field is not empty
if(isset($_POST['email']) && !empty($_POST['email'])){

    // a) Sanitize the email to remove unwanted characters
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // b) Validate the email format
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);

    // c) Initialize error message
    $error = '';

    // d) If email is invalid, show an error
    if(!$email){
        $error .= "<p>Invalid email address. Please type a valid email address!</p>"; 
    } else {
        // e) Check if this email exists in the database
        // Note: Replace `st-email` with your actual column name in `users` table
        $sel_query = "SELECT * FROM `users` WHERE `st-email`='".$email."'";
        $result = mysqli_query($conn, $sel_query);

        // f) Count the number of rows returned
        $row = mysqli_num_rows($result);

        // g) If no user exists with this email, show error
        if($row == 0){
            $error .= "<p>No user is registered with this email address!</p>";
        }
    }
}

// 6️⃣ If there is any error, display it and stop execution
if(!empty($error)){
    echo "<div class='error'>".$error."</div>
    <br> <a href='javascript:history.go(-1)'>Go Back</a>";
} else {
    // 7️⃣ Generate expiration date for reset link (1 day from now)
    $expFormat = mktime(
        date("H"), date("i"), date("s"), date("m"), date("d")+1, date("Y")
    );
    $expDate = date("Y-m-d H:i:s", $expFormat); // Format: YYYY-MM-DD HH:MM:SS

    // 8️⃣ Generate a unique key for password reset
    $key = md5((20148*2) . $email); // Basic hash
    $addKey = substr(md5(uniqid(rand(),1)),3,10); // Add randomness
    $key = $key . $addKey; // Final key

    // 9️⃣ Insert the reset request into a temporary table
    // Make sure `password_reset_temp` table exists with columns: email, key, expDate
    mysqli_query($conn,
        "INSERT INTO `password_reset_temp` (`email`, `key`, `expDate`)
        VALUES ('".$email."', '".$key."', '".$expDate."');"
    );

    // 🔟 Prepare the email body (HTML format)
    // --- NOTE FOR BEGINNERS ---
    // Here you can either:
    // 1) Copy this email body as-is (safe and works)
    // 2) Customize the text, styling, add your own message, logo, colors, etc.
    // 3) Always include the reset link like this: ?key=...&email=...&action=reset
    $output = '<p>Dear user,</p>';
    $output .= '<p>Please click on the following link to reset your password:</p>';
    $output .= '<p>-------------------------------------------------------------</p>';
    $output .= '<p><a href="http://localhost/Stock-tracker-new-ver/forgot-password/reset-password.php?key='.$key.'&email='.$email.'&action=reset" target="_blank">
    http://localhost/Stock-tracker-new-ver/forgot-password/reset-password.php?key='.$key.'&email='.$email.'&action=reset</a></p>';		
    $output .= '<p>-------------------------------------------------------------</p>';
    $output .= '<p>The link will expire after 1 day for security reasons.</p>';
    $output .= '<p>If you did not request this, no action is needed.</p>';   	
    $output .= '<p>Thanks,</p>';
    $output .= '<p>AllPHPTricks Team</p>';

    // ✅ This is the variable PHPMailer will use as email content
    $body = $output;
    $subject = "Password Recovery - Stock-Tracker"; // You can also change subject text

    $email_to = $email; // Recipient email
    $fromserver = "info.ibrahim172@gmail.com"; // Your sender email

    // 11️⃣ Setup PHPMailer
    $mail = new PHPMailer();
    $mail->IsSMTP(); // Use SMTP
    $mail->Host = "smtp.gmail.com"; // Gmail SMTP server
    $mail->SMTPAuth = true; // Enable SMTP authentication
    $mail->Username = "info.ibrahim172@gmail.com"; // SMTP username
    $mail->Password = "wcirqrecufsihtgd"; // SMTP app password (generated from Gmail)
    $mail->Port = 587; // TLS port
    $mail->SMTPSecure = "tls"; // Encryption
    $mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
    $mail->IsHTML(true); // Set email format to HTML
    $mail->From = "info.ibrahim172@gmail.com"; // From email
    $mail->FromName = "Ibrahim Khalil"; // From name
    $mail->Sender = $fromserver; // Return-path header
    $mail->Subject = $subject; // Email subject
    $mail->Body = $body; // Email content
    $mail->AddAddress($email_to); // Recipient

    // 12️⃣ Send the email and handle errors
    if(!$mail->Send()){
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
       header("Location: ../index.php?msg=email_sent");
exit();
    }
}
?>


