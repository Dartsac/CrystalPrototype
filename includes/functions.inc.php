<?php

function emptyInputSignup($name, $email, $pwd, $pwdRepeat, $birthDate, $birthTime, $birthPlace) {
	$result;
	if (empty($name) || empty($email) || empty($pwd) || empty($pwdRepeat) || empty($birthDate) || empty($birthTime) || empty($birthPlace)) {
		$result = true;
	} else {
		$result = false;
	}
	return $result;
}

function invalidUsername($name) {
	$result;
	if (!preg_match("/^[a-zA-Z0-9]*$/", $name)) {
		$result = true;
	} else {
		$result = false;
	}
	return $result;
}

function invalidEmail($email) {
	$result;
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$result = true;
	} else {
		$result = false;
	}
	return $result;
}

function pwdMatch($pwd, $pwdRepeat) {
	$result;
	if ($pwd !== $pwdRepeat) {
		$result = true;
	} else {
		$result = false;
	}
	return $result;
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

	if ($row = mysqli_fetch_assoc($resultsData)) {
		return $row;
	} else {
		$result = false;
		return $result;
	}

	mysqli_stmt_close($stmt);

}

function createUser($conn, $name, $email, $pwd, $birthDate, $birthTime, $birthPlace) {
	$sql = "INSERT INTO users(usersName, usersEmail, usersPwd, usersBirthDate, usersBirthTime, usersPlace) VALUES (?, ?, ?, ?, ?, ?);";
	$stmt = mysqli_stmt_init($conn);
	if (!mysqli_stmt_prepare($stmt, $sql)) {
		header("location: ../signup.php?error=stmtfailed");
		exit();
	}

	$hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

	mysqli_stmt_bind_param($stmt, "ssssss", $name, $email, $hashedPwd, $birthDate, $brithTime, $brithPlace);

	mysqli_stmt_execute($stmt);

	mysqli_stmt_close($stmt);

	header("location: ../signup.php?error=none");
}

function emptyInputLogin($name, $pwd) {
	$result;
	if (empty($name) || empty($pwd)) {
		$result = true;
	} else {
		$result = false;
	}
	return $result;
}

function loginUser($conn, $name, $pwd) {
	$userNameExists = userNameExists($conn, $name, $name);

	if ($userNameExists === false) {
		header("location: ../login.php?error=wronglogin");
		exit;
	}

	$pwdHashed = $userNameExists["usersPwd"];
	$checkPwd = password_verify($pwd, $pwdHashed);

	if ($checkPwd === false) {
		header("location: ../login.php?error=wronglogin");
		exit();
	}
	elseif ($checkPwd) {
		session_start();
		$_SESSION["userid"] = $userNameExists["usersId"];
		$_SESSION["username"] = $userNameExists["usersName"];
		$_SESSION["user-day"] = $userNameExists["usersBirthDate"];
		$_SESSION["user-time"] = $userNameExists["usersBirthTime"];
		$_SESSION["user-place"] = $userNameExists["usersPlace"];

		header("location: ../profile.php");
		exit();
	}
}
