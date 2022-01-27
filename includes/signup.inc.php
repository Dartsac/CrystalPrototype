<?php
if (isset($_POST["Submit"])) {
	$name = $_POST["Display_Name"];
	$email = $_POST["Email"];
	$pwd = $_POST["Password"];
	$pwdRepeat = $_POST["Password_Repeat"];

	$birthDate = $_POST["Birth_Date"];
	$birthTime = $_POST["Birth_Time"];
	$birthPlace = $_POST["Birth_Place"];

	$profileImg = "img/final_headshot.png";
	$bannerImg = "img/IsaacBanner.gif";

	echo $name, $email, $pwd, $pwdRepeat, $birthDate, $birthTime, $birthPlace, $profileImg, $bannerImg;
	require_once 'dbh.inc.php';
	require_once 'functions.inc.php';

	if(emptyInputSignup($name, $email, $pwd, $pwdRepeat, $birthDate, $birthTime, $birthPlace) !== false) {
		header("location: ../signup.php?error=emptyinput");
		exit();
	}

	if(invalidUsername($name) !== false) {
		header("location: ../signup.php?error=invalidusername");
		exit();
	}

	if(invalidEmail($email) !== false) {
		header("location: ../signup.php?error=invalidemail");
		exit();
	}

	if(pwdMatch($pwd, $pwdRepeat) !== false) {
		header("location: ../signup.php?error=passwordnotmatch");
		exit();
	}

	if(userNameExists($conn, $name, $email) !== false) {
		header("location: ../signup.php?error=usernametaken");
		exit();
	}
	createUser($conn, $name, $email, $pwd, $birthDate, $birthTime, $birthPlace, $profileImg, $bannerImg);


} else {
	header("location: ../signup.php?error=emptyinput");
	exit();
}
