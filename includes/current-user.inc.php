<?php
/*
 * Loads the logged-in user and their chart for pages that need them
 * (profile, chart, shop). Include it before header.php so it can redirect.
 *
 * Sets: $conn, $user, $chart (null if the birth date can't be used), $today.
 */

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

require_once __DIR__ . '/dbh.inc.php';
require_once __DIR__ . '/functions.inc.php';

if (!isset($_SESSION["userid"])) {
	header("location: login.php");
	exit();
}

$user = getUserById($conn, $_SESSION["userid"]);
if ($user === false) {
	session_unset();
	session_destroy();
	header("location: login.php");
	exit();
}

if (!array_key_exists('usersLat', $user)) {
	http_response_code(500);
	die("The users table is missing the birth location columns. Run sql/migrate-001-birth-location.sql (see README.md).");
}

// Accounts created before birth places were resolved: look the city up once per session.
if (($user['usersLat'] === null || $user['usersTimezone'] === null) && !empty($user['usersPlace']) && empty($_SESSION['geocode_attempted'])) {
	$_SESSION['geocode_attempted'] = true;
	$found = geocodePlace($user['usersPlace']);
	if ($found !== null) {
		updateUserLocation($conn, (int) $user['usersId'], $found['lat'], $found['lng'], $found['tz']);
		$user['usersLat'] = $found['lat'];
		$user['usersLng'] = $found['lng'];
		$user['usersTimezone'] = $found['tz'];
	}
}

$chart = userChart($user);
$today = new DateTimeImmutable('now');

/* A picture from the user's own img/<username>/ folder, or a shared default. */
function userImage($user, $file, $default) {
	$path = 'img/' . $user['usersName'] . '/' . $file;
	return is_file(__DIR__ . '/../' . $path) ? $path : $default;
}
