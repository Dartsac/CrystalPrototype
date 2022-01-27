<?php
  include 'header.php';
?>
<section>

  <header class="header" id="nav-bar">
      <a href="index.php"><img src="img/logo.png" class="logo"/></a>
      <div id="nav">
        <a href="#mission" style="margin-left:25px; text-decoration: none; color: #112637;">OUR MISSION</a>
        <a href="#about" style="margin-left:25px; text-decoration: none; color: #112637;">ABOUT US</a>
        <a href="#contact" style="margin-left:25px; text-decoration: none; color: #112637;">CONTACT</a>
        <?php
          if (isset($_SESSION["username"])) {
            echo "<a href='profile.php' style='margin-left:25px; text-decoration: none;color: #112637;'> PROFILE</a>";
            echo "<a href='includes/logout.inc.php' style='margin-left:25px; text-decoration: none;font-weight: 600; color: #112637;'> LOG OUT</a>";
          } else {
            echo "<a href='login.php' style='margin-left:25px; text-decoration: none;font-weight: 600; color: #112637;'> LOG IN</a>";
          }
        ?>
      </div>
  </header>

  <div class="Aligner">
    <div class="Aligner-item">

  <h2> Sign Up</h2>
  <form action="includes/signup.inc.php" method="post">
      <p style="text-align:center;">Set a display name</p>
      <input type="text" placeholder="Display Name..." name="Display_Name">
      <br>
      <input type="email" placeholder="Email..." name="Email">
      <br>
      <input type="password" placeholder="Password..." name="Password">
      <br>
      <input type="password" placeholder="Re-Enter Password..." name="Password_Repeat">
      <br><br>
      <div class="user-data info">
        <p style="text-align:center;"> I was born in</p>
        <div style="">
          <input required="" type="text" placeholder="a city" name="Birth_Place" id="place" value="">
        </div>
        <p style="text-align:center;">on</p>
        <input required="" type="date" placeholder="yyyy-mm-dd" pattern="\d\d\d\d-\d\d-\d\d" min="1500-01-01" max="2500-12-31" name="Birth_Date" value="">
        <p>at </p>
        <input type="time" placeholder="hh:mm AM" pattern="(\d?\d):(\d\d)( (AM|PM|am|pm))?" name="Birth_Time" id="birthTime" value="">
      </div>

      <br><br>
      <button type="submit" name="Submit">Sign Up</button>
  </form>

  <p style="text-align:center;">Already have an account? </p>
  <div style="text-align:center;">
    <a href="login.php">Log in</a>
  </div>

  </div>
</div>

  <!-- here is where we will handle error messages -->
  <?php
    if(isset($_GET["error"])) {
      if ($_GET["error"] == "emptyinput") {
        echo "<p>Fill in all fields.</p>";
      }
      elseif ($_GET["error"] == "invalidusername") {
        echo "<p>Choose a proper username. No special characters.</p>";
      }
      elseif ($_GET["error"] == "invalidemail") {
        echo "<p>Choose a proper email</p>";
      }
      elseif ($_GET["error"] == "passwordnotmatch") {
        echo "<p>Passwords did not match.</p>";
      }
      elseif ($_GET["error"] == "usernametaken") {
        echo "<p>This username is taken.</p>";
      }
      elseif ($_GET["error"] == "stmtfailed") {
        echo "<p>Something went wrong, try again!</p>";
      }
      elseif ($_GET["error"] == "none") {
        echo "<p>You have successfuly signed up to Crystal!</p>";
      }
    }
   ?>
</section

<?php
  include 'footer.php';
?>
