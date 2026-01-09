<?php
if (isset($_POST["Submit"])) {

  $name = trim($_POST["Display_Name"] ?? "");
  $pwd = $_POST["Password"] ?? "";

  require_once 'dbh.inc.php';
  require_once 'functions.inc.php';

  if (emptyInputLogin($name, $pwd) !== false) {
    header("location: ../login.php?error=emptyinput");
    exit();
  }

  loginUser($conn, $name, $pwd);

} else {
  header("location: ../login.php");
  exit();
}
