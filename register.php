<?php
 	require_once 'PHPMailer/PHPMailerAutoload.php';
	include('conn.php');
	session_start();

	if ($_SERVER["REQUEST_METHOD"] == "POST") {

	function check_input($data){
		$data=trim($data);
		$data=stripslashes($data);
		$data=htmlspecialchars($data);
		return $data;
	}

	$email=check_input($_POST['email']);
	$password=md5(check_input($_POST['password']));

	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  		$_SESSION['sign_msg'] = "Invalid email format";
  		header('location:signup.php');
	}

	else{

		$query=mysqli_query($conn,"select * from user where email='$email'");
		if(mysqli_num_rows($query)>0){
			$_SESSION['sign_msg'] = "Email already taken";
  			header('location:signup.php');
		}
		else{
		//depends on how you set your verification code
		$set='123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$code=substr(str_shuffle($set), 0, 12);

		mysqli_query($conn,"insert into user (email, password, code) values ('$email', '$password', '$code')");
		$uid=mysqli_insert_id($conn);
		//default value for our verify is 0, means it is unverified


				// Fetching data that is entered by the user
		$from = "youremail@gmail.com"; //use sender id here like I have this one
		// sometimes used email can create erros so please be sure you use the authentication free accounts.
		$password = "youremailpassword"; //actual password of mail id which you want to use send mail to user mail
		$to_id = $_POST['email'];
		$message = "Hii!" .$email. "Please Click Below link to activate your Account:<br> http://localhost/send_mail/activate.php?uid&code=$code";//add code=$code at last 

		// Configuring SMTP server settings
		date_default_timezone_set('Etc/UTC');
		$mail = new PHPMailer(); 
		$mail->SMTPOptions = array(
		    'ssl' => array(
		        'verify_peer' => false,
		        'verify_peer_name' => false,
		        'allow_self_signed' => true
		    )
		);
		$mail->SMTPDebug = SMTP::DEBUG_SERVER;
		$mail->SMTPKeepAlive = true;   
		$mail->Mailer = "smtp"; // don't change the quotes!
		$mail->isSMTP();
		$mail->Host =gethostbyname('ssl://smtp.gmail.com');
		$mail->Host = 'smtp.gmail.com';
		$mail->Port = 465;
		$mail->SMTPSecure = 'ssl';
		$mail->SMTPAuth = true;
		$mail->Username = $from;
		$mail->Password = $password;
		$mail->FromName = "Arjun Pariyar";

		// Email Sending Details
		$mail->addAddress($to_id);
		$mail->msgHTML($message);

		// Success or Failure
		if (!$mail->send()) {
		$error = "Mailer Error: " . $mail->ErrorInfo;
		echo '<p>'.$error.'</p>';
		}
		else 
		{
		$_SESSION['sign_msg'] = "Verification code sent to your email.";
  		header('location:signup.php');
  		}
  	}
  	}
  	}
?>