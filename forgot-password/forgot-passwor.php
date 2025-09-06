<?php
include 'connection.php';
if(isset($_POST['email']) && (!empty($_POST['email']))){
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if(!$email){
        $error .="<p>Invalid email adress please type a valid email adress !</p>"; 
    }else{
        $sel_query = "SELECT * FROM `users` WHERE email='".$email."'";
        $result = mysqli_query($con,$sel_query);
        $row = mysqli_num_rows($results);
        if($row==""){
            $error.="<p>No User is registered with this email address!</p>";

        }
    }
    
}

if($error!=""){
    echo "<div class='error'>".$error."</div>
    <br> <a href='javascript:history.go(-1)'>Go Back</a>";
    ;
}else{
    $expFormat = mktime(
        date("H"), date("i"), date("m"), date("d")+1, date("y")
    );
    $expDate = date("Y-m-d H:i:s",$expFormat);
    $key = md5(20148*2+$email);
    $addkey = substr(md5(uniqid(rand(),1)),3,10);
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