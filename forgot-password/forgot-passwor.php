<?php
// ============================================
// FORGOT PASSWORD SCRIPT USING PHPMailer
// ============================================
if (!defined('APP_INIT')) {
    define('APP_INIT', true);
}

include 'connection.php';
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = '';
$error = '';
$key = '';

if (isset($_POST['email']) && !empty($_POST['email'])) {
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    $error = '';

    if (!$email) {
        $error .= "<p>Invalid email address. Please type a valid email address!</p>";
    } else {
        // Check if the email exists in the users table
        $sel_query = "SELECT * FROM `users` WHERE `st-email`='" . $email . "'";
        $result = mysqli_query($conn, $sel_query);
        $row = mysqli_num_rows($result);

        if ($row == 0) {
            $error .= "<p>No user is registered with this email address!</p>";
        }
    }
}

if (!empty($error)) {
    echo "<div class='error'>" . $error . "</div>
    <br> <a href='javascript:history.go(-1)'>Go Back</a>";
} else {
    // Generate expiration date for reset link (1 day from now)
    $expFormat = mktime(
        date("H"), date("i"), date("s"), date("m"), date("d") + 1, date("Y")
    );
    $expDate = date("Y-m-d H:i:s", $expFormat);

    // Generate a unique key for password reset
    $key = md5((20148 * 2) . $email);
    $addKey = substr(md5(uniqid(rand(), 1)), 3, 10);
    $key = $key . $addKey;

    // Insert the reset request into the password_reset_temp table
    mysqli_query($conn,
        "INSERT INTO `password_reset_temp` (`email`, `key`, `expDate`)
        VALUES ('" . $email . "', '" . $key . "', '" . $expDate . "');"
    );

    // Prepare the email body
    $output = '<p>Dear user,</p>';
    $output .= '<p>Please click on the following link to reset your password:</p>';
    $output .= '<p>-------------------------------------------------------------</p>';
    $output .= '<p><a href="http://localhost/Stock-tracker-new-ver/forgot-password/reset-password.php?key=' . $key . '&email=' . $email . '&action=reset" target="_blank">
    http://localhost/Stock-tracker-new-ver/forgot-password/reset-password.php?key=' . $key . '&email=' . $email . '&action=reset</a></p>';
    $output .= '<p>-------------------------------------------------------------</p>';
    $output .= '<p>The link will expire after 1 day for security reasons.</p>';
    $output .= '<p>If you did not request this, no action is needed.</p>';
    $output .= '<p>Thanks,</p>';
    $output .= '<p>AllPHPTricks Team</p>';

    $body = $output;
    $subject = "Password Recovery - Stock-Tracker";
    $email_to = $email;
    $fromserver = "info.ibrahim172@gmail.com";

    // Configure and send email using PHPMailer
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;
    $mail->Username = "info.ibrahim172@gmail.com";
    $mail->Password = "wcirqrecufsihtgd";
    $mail->Port = 587;
    $mail->SMTPSecure = "tls";
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    ); // Add this line to disable SSL verification in production is not recommended
    $mail->IsHTML(true);
    $mail->From = "info.ibrahim172@gmail.com";
    $mail->FromName = "Ibrahim Khalil";
    $mail->Sender = $fromserver;
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AddAddress($email_to);

    if (!$mail->Send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
        header("Location: ../index.php?msg=email_sent");
        exit();
    }
}
?>

