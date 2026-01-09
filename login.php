<?php
  include 'header.php';
  $showSiteLinks = true;
  include 'nav.php';
?>

<main class="auth">
  <div class="auth-art" aria-hidden="true">
    <blockquote>Spirituality is about understanding deeper of oneself.</blockquote>
  </div>
  <div class="auth-form">
    <section class="auth-card">
      <h2>Log In</h2>
      <p>Welcome back. Your guide is waiting.</p>

      <!-- here is where I will handle error messages -->
      <?php
        if (isset($_GET["error"])) {
          if ($_GET["error"] == "emptyinput") {
            echo "<p class='form-error' role='alert'>Fill in all fields of the login.</p>";
          }
          elseif ($_GET["error"] == "wronglogin") {
            echo "<p class='form-error' role='alert'>Incorrect login information.</p>";
          }
        }
       ?>

      <form action="includes/login.inc.php" method="post">
        <div class="field">
          <label for="loginName">Username or email</label>
          <input type="text" name="Display_Name" id="loginName" autocomplete="username" value="">
        </div>
        <div class="field">
          <label for="loginPassword">Password</label>
          <input type="password" name="Password" id="loginPassword" autocomplete="current-password">
        </div>
        <button type="submit" name="Submit" class="btn btn-primary btn-block">Log In</button>
      </form>

      <p class="auth-switch">Don't have an account? <a href="signup.php">Sign up</a></p>
    </section>
  </div>
</main>

<?php
  include 'footer.php';
?>
