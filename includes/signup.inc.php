<?php
if (!isset($_POST["Submit"])) {
	header("location: ../signup.php");
	exit();
}

session_start();

$name = trim($_POST["Display_Name"] ?? "");
$email = trim($_POST["Email"] ?? "");
$pwd = $_POST["Password"] ?? "";
$pwdRepeat = $_POST["Password_Repeat"] ?? "";

$birthDate = $_POST["Birth_Date"] ?? "";
$birthTime = $_POST["Birth_Time"] ?? "";
$birthPlace = trim($_POST["Birth_Place"] ?? "");
$birthLat = $_POST["Birth_Lat"] ?? "";
$birthLng = $_POST["Birth_Lng"] ?? "";
$birthTz = $_POST["Birth_Timezone"] ?? "";

require_once 'dbh.inc.php';
require_once 'functions.inc.php';

// Send the user back to the form, keeping what they typed (never the password).
function signupError($error) {
	global $name, $email, $birthDate, $birthTime, $birthPlace, $birthLat, $birthLng, $birthTz;
	$_SESSION["signup_old"] = [
		"Display_Name" => $name, "Email" => $email, "Birth_Date" => $birthDate, "Birth_Time" => $birthTime,
		"Birth_Place" => $birthPlace, "Birth_Lat" => $birthLat, "Birth_Lng" => $birthLng, "Birth_Timezone" => $birthTz,
	];
	header("location: ../signup.php?error=" . $error);
	exit();
}

if (emptyInputSignup($name, $email, $pwd, $pwdRepeat, $birthDate, $birthTime, $birthPlace) !== false) {
	signupError("emptyinput");
}

if (invalidUsername($name) !== false) {
	signupError("invalidusername");
}

if (invalidEmail($email) !== false) {
	signupError("invalidemail");
}

if (pwdMatch($pwd, $pwdRepeat) !== false) {
	signupError("passwordnotmatch");
}

if (invalidBirthDate($birthDate)) {
	signupError("invaliddate");
}

if (invalidBirthTime($birthTime)) {
	signupError("invalidtime");
}

if (userNameExists($conn, $name, $email) !== false) {
	signupError("usernametaken");
}

$place = resolveBirthPlace($birthPlace, $birthLat, $birthLng, $birthTz);
if ($place === null) {
	signupError("placenotfound");
}

$id = createUser($conn, $name, $email, $pwd, $birthDate, $birthTime, $place);
unset($_SESSION["signup_old"]);

startUserSession(getUserById($conn, $id));
header("location: ../profile.php");
exit();
