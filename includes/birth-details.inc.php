<?php
if (!isset($_POST["Submit"])) {
	header("location: ../birth-details.php");
	exit();
}

session_start();

if (!isset($_SESSION["userid"])) {
	header("location: ../login.php");
	exit();
}

require_once 'dbh.inc.php';
require_once 'functions.inc.php';

$birthDate = $_POST["Birth_Date"] ?? "";
$birthTime = $_POST["Birth_Time"] ?? "";
$birthPlace = trim($_POST["Birth_Place"] ?? "");

if ($birthDate === "" || $birthTime === "" || $birthPlace === "") {
	header("location: ../birth-details.php?error=emptyinput");
	exit();
}

if (invalidBirthDate($birthDate)) {
	header("location: ../birth-details.php?error=invaliddate");
	exit();
}

if (invalidBirthTime($birthTime)) {
	header("location: ../birth-details.php?error=invalidtime");
	exit();
}

$place = resolveBirthPlace($birthPlace, $_POST["Birth_Lat"] ?? "", $_POST["Birth_Lng"] ?? "", $_POST["Birth_Timezone"] ?? "");
if ($place === null) {
	header("location: ../birth-details.php?error=placenotfound");
	exit();
}

updateBirthDetails($conn, $_SESSION["userid"], $birthDate, $birthTime, $place);
header("location: ../about.php?updated=1");
exit();
