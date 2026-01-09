<?php
  include 'header.php';
  require_once 'includes/astrology.inc.php';

  $old = $_SESSION["signup_old"] ?? [];
  unset($_SESSION["signup_old"]);
  $birthValues = $old;

  $signupErrors = [
    "emptyinput" => "Fill in all fields.",
    "invalidusername" => "Choose a proper username. No special characters.",
    "invalidemail" => "Choose a proper email.",
    "passwordnotmatch" => "Passwords did not match.",
    "invaliddate" => "Choose a real birth date between " . ASTRO_MIN_YEAR . " and today.",
    "invalidtime" => "Choose your birth time.",
    "placenotfound" => "We couldn't find that city. Try picking it from the suggestions, or add the state or country.",
    "usernametaken" => "This username is taken.",
    "stmtfailed" => "Something went wrong, try again!",
  ];
  $error = $_GET["error"] ?? null;

  $showSiteLinks = true;
  include 'nav.php';
?>

<main class="auth">
  <div class="auth-art" aria-hidden="true">
    <blockquote>Your guide starts with the sky at the minute you were born.</blockquote>
  </div>
  <div class="auth-form">
    <section class="auth-card">
      <h2>Sign Up</h2>
      <p>Create your account and get your individualized guide.</p>
      <?php if ($error !== null && isset($signupErrors[$error])) { ?>
        <p class="form-error" role="alert"><?php echo htmlspecialchars($signupErrors[$error], ENT_QUOTES); ?></p>
      <?php } ?>
      <form action="includes/signup.inc.php" method="post">
        <div class="field">
          <label for="displayName">Display name</label>
          <input type="text" name="Display_Name" id="displayName" autocomplete="username" value="<?php echo htmlspecialchars($old["Display_Name"] ?? "", ENT_QUOTES); ?>">
        </div>
        <div class="field">
          <label for="email">Email</label>
          <input type="email" name="Email" id="email" autocomplete="email" value="<?php echo htmlspecialchars($old["Email"] ?? "", ENT_QUOTES); ?>">
        </div>
        <div class="field-row">
          <div class="field">
            <label for="password">Password</label>
            <input type="password" name="Password" id="password" autocomplete="new-password">
          </div>
          <div class="field">
            <label for="passwordRepeat">Re-enter password</label>
            <input type="password" name="Password_Repeat" id="passwordRepeat" autocomplete="new-password">
          </div>
        </div>

        <p class="fieldset-title">Your birth</p>
        <?php include 'birth-fields.php'; ?>

        <button type="submit" name="Submit" class="btn btn-primary btn-block">Sign Up</button>
      </form>

      <p class="auth-switch">Already have an account? <a href="login.php">Log in</a></p>
    </section>
  </div>
</main>

<?php
  include 'footer.php';
?>
