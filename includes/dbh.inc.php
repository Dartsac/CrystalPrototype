<?php

// Defaults match a local MAMP/XAMPP/Homebrew MySQL. Override with environment variables if needed.
$serverName = getenv('CRYSTAL_DB_HOST') ?: "localhost";
$dBUsername = getenv('CRYSTAL_DB_USER') ?: "root";
$dBPassword = getenv('CRYSTAL_DB_PASSWORD') ?: "";
$dbName = getenv('CRYSTAL_DB_NAME') ?: "Crystal";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
	$conn = mysqli_connect($serverName, $dBUsername, $dBPassword, $dbName);
	mysqli_set_charset($conn, 'utf8mb4');
} catch (mysqli_sql_exception $e) {
	http_response_code(500);
	die("Connection failed: " . htmlspecialchars($e->getMessage(), ENT_QUOTES));
}
