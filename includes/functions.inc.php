<?php

require_once __DIR__ . '/astrology.inc.php';
require_once __DIR__ . '/geocode.inc.php';

function emptyInputSignup($name, $email, $pwd, $pwdRepeat, $birthDate, $birthTime, $birthPlace) {
	return empty($name) || empty($email) || empty($pwd) || empty($pwdRepeat) || empty($birthDate) || empty($birthTime) || empty($birthPlace);
}

function invalidUsername($name) {
	return !preg_match("/^[a-zA-Z0-9]{1,64}$/", $name);
}

function invalidEmail($email) {
	return !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 128;
}

function pwdMatch($pwd, $pwdRepeat) {
	return $pwd !== $pwdRepeat;
}

/* A real calendar date between ASTRO_MIN_YEAR and today. */
function invalidBirthDate($birthDate) {
	if (!is_string($birthDate) || !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $birthDate, $m)) {
		return true;
	}
	if (!checkdate((int) $m[2], (int) $m[3], (int) $m[1])) {
		return true;
	}
	return (int) $m[1] < ASTRO_MIN_YEAR || $birthDate > date('Y-m-d');
}

/* 24-hour "HH:MM" (or "HH:MM:SS"), as sent by <input type="time">. */
function invalidBirthTime($birthTime) {
	return !is_string($birthTime) || !preg_match('/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', $birthTime);
}

/*
 * Resolve the birth place to coordinates and a timezone.
 * Uses the place the user picked from the suggestions when it is valid,
 * otherwise looks the typed text up. Returns null if the place can't be found.
 */
function resolveBirthPlace($placeText, $lat, $lng, $tz) {
	$placeText = trim((string) $placeText);
	if ($placeText === '' || mb_strlen($placeText) > 255) {
		return null;
	}
	if (is_numeric($lat) && is_numeric($lng) && (float) $lat >= -90 && (float) $lat <= 90
		&& (float) $lng >= -180 && (float) $lng <= 180 && isValidTimezone($tz)) {
		return ['place' => $placeText, 'lat' => (float) $lat, 'lng' => (float) $lng, 'tz' => $tz];
	}
	return geocodePlace($placeText);
}

function userNameExists($conn, $name, $email) {
	$sql = "SELECT * FROM users WHERE usersName = ? OR usersEmail = ?;";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt, $sql)) {
		header("location: ../signup.php?error=stmtfailed");
		exit();
	}

	mysqli_stmt_bind_param($stmt, "ss", $name, $email);
	mysqli_stmt_execute($stmt);

	$resultsData = mysqli_stmt_get_result($stmt);
	$row = mysqli_fetch_assoc($resultsData);
	mysqli_stmt_close($stmt);

	return $row ? $row : false;
}

function getUserById($conn, $id) {
	$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE usersId = ?;");
	mysqli_stmt_bind_param($stmt, "i", $id);
	mysqli_stmt_execute($stmt);
	$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
	mysqli_stmt_close($stmt);
	return $row ? $row : false;
}

/* Creates the user and returns their new id. $place comes from resolveBirthPlace(). */
function createUser($conn, $name, $email, $pwd, $birthDate, $birthTime, $place) {
	$sql = "INSERT INTO users (usersName, usersEmail, usersPwd, usersBirthDate, usersBirthTime, usersPlace, usersLat, usersLng, usersTimezone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt, $sql)) {
		header("location: ../signup.php?error=stmtfailed");
		exit();
	}

	$hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
	$placeName = $place['place'];
	$lat = $place['lat'];
	$lng = $place['lng'];
	$tz = $place['tz'];

	mysqli_stmt_bind_param($stmt, "ssssssdds", $name, $email, $hashedPwd, $birthDate, $birthTime, $placeName, $lat, $lng, $tz);
	mysqli_stmt_execute($stmt);
	$id = mysqli_insert_id($conn);
	mysqli_stmt_close($stmt);

	return $id;
}

function updateBirthDetails($conn, $id, $birthDate, $birthTime, $place) {
	$stmt = mysqli_prepare($conn, "UPDATE users SET usersBirthDate = ?, usersBirthTime = ?, usersPlace = ?, usersLat = ?, usersLng = ?, usersTimezone = ? WHERE usersId = ?;");
	$placeName = $place['place'];
	$lat = $place['lat'];
	$lng = $place['lng'];
	$tz = $place['tz'];
	mysqli_stmt_bind_param($stmt, "sssddsi", $birthDate, $birthTime, $placeName, $lat, $lng, $tz, $id);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
}

function updateUserLocation($conn, $id, $lat, $lng, $tz) {
	$stmt = mysqli_prepare($conn, "UPDATE users SET usersLat = ?, usersLng = ?, usersTimezone = ? WHERE usersId = ?;");
	mysqli_stmt_bind_param($stmt, "ddsi", $lat, $lng, $tz, $id);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
}

function startUserSession($user) {
	if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}
	session_regenerate_id(true);
	$_SESSION["userid"] = (int) $user["usersId"];
	$_SESSION["username"] = $user["usersName"];
}

function emptyInputLogin($name, $pwd) {
	return empty($name) || empty($pwd);
}

function loginUser($conn, $name, $pwd) {
	$userNameExists = userNameExists($conn, $name, $name);

	if ($userNameExists === false) {
		header("location: ../login.php?error=wronglogin");
		exit();
	}

	$pwdHashed = $userNameExists["usersPwd"];
	$checkPwd = password_verify($pwd, $pwdHashed);

	if ($checkPwd === false) {
		header("location: ../login.php?error=wronglogin");
		exit();
	}

	startUserSession($userNameExists);
	header("location: ../profile.php");
	exit();
}

/* Birth time as "HH:MM", or null when it was never saved. */
function userBirthTime($user) {
	$time = $user['usersBirthTime'] ?? null;
	if ($time === null || !preg_match('/^(\d{1,2}):(\d{2})/', $time, $m)) {
		return null;
	}
	return str_pad($m[1], 2, '0', STR_PAD_LEFT) . ':' . $m[2];
}

/* The user's natal chart, or null if their birth date can't be used. */
function userChart($user) {
	$lat = $user['usersLat'] ?? null;
	$lng = $user['usersLng'] ?? null;
	return astroBuildChart(
		substr((string) ($user['usersBirthDate'] ?? ''), 0, 10),
		userBirthTime($user),
		$lat === null ? null : (float) $lat,
		$lng === null ? null : (float) $lng,
		$user['usersTimezone'] ?? null
	);
}

/* Escape for HTML output. */
function e($value) {
	return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
