<?php
  include 'header.php';
?>
<header class="header" id="nav-bar">
    <a href="index.php"><img src="img/logo.png" class="logo"/></a>
    <div id="nav">
      <a href="#mission" style="margin-left:25px; text-decoration: none; color: #112637;">OUR MISSION</a>
      <a href="#about" style="margin-left:25px; text-decoration: none; color: #112637;">ABOUT US</a>
      <a href="#contact" style="margin-left:25px; text-decoration: none; color: #112637;">CONTACT</a>
      <?php
        if (isset($_SESSION["username"])) {
          echo "<a href='profile.php'> PROFILE</a>";
          echo "<a href='includes/logout.inc.php'> LOG OUT</a>";
        } else {
          echo "<a href='login.php' style='margin-left:25px; text-decoration: none;font-weight: 600; color: #112637;'> LOG IN</a>";
        }
      ?>
    </div>
</header>

<div class="Aligner">
  <div class="Aligner-item">
    <section>
      <h2>Log In</h2>
      <form action="includes/login.inc.php" method="post">
          <input type="text" placeholder="Username/Email..." name="Display_Name" value="">
          <br>
          <input type="password" placeholder="Password..." name="Password">
          <br><br>
          <button type="submit" name="Submit">Log In</button>
      </form>

      <p style="text-align:center;">Don't have an account? </p>
      <div style="text-align:center;">
        <a href="signup.php">Sign up</a>
      </div>

      <!-- here is where I will handle error messages -->
      <?php
        if(isset($_GET["error"])) {
          if ($_GET["error"] == "emptyinput") {
            echo "<p>Fill in all fields of the login.</p>";
          }
          elseif ($_GET["error"] == "wronglogin") {
            echo "<p>Incorrect login information.</p>";
          }
        }
       ?>

    </section>
  </div>
</div>
