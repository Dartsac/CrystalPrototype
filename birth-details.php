<?php
  require 'includes/current-user.inc.php';

  include 'header.php';
  include 'nav.php';

  $errors = [
    "emptyinput" => "Fill in all fields.",
    "invaliddate" => "Choose a real birth date between " . ASTRO_MIN_YEAR . " and today.",
    "invalidtime" => "Choose your birth time.",
    "placenotfound" => "We couldn't find that city. Try picking it from the suggestions, or add the state or country.",
  ];
  $error = $_GET["error"] ?? null;

  $birthValues = [
    "Birth_Date" => substr((string) $user["usersBirthDate"], 0, 10),
    "Birth_Time" => userBirthTime($user),
    "Birth_Place" => $user["usersPlace"],
    "Birth_Lat" => $user["usersLat"],
    "Birth_Lng" => $user["usersLng"],
    "Birth_Timezone" => $user["usersTimezone"],
  ];
?>

<main class="auth">
  <div class="auth-art" aria-hidden="true">
    <blockquote>Your chart is calculated to the very minute you were born.</blockquote>
  </div>
  <div class="auth-form">
    <section class="auth-card">
      <h2>Birth Details</h2>
      <p>Your chart, guide, and crystal picks are calculated from these.</p>
      <?php if ($error !== null && isset($errors[$error])) { ?>
        <p class="form-error" role="alert"><?php echo e($errors[$error]); ?></p>
      <?php } ?>
      <form action="includes/birth-details.inc.php" method="post">
        <?php include 'birth-fields.php'; ?>
        <button type="submit" name="Submit" class="btn btn-primary btn-block">Save</button>
      </form>
      <p class="auth-switch"><a href="about.php">Back to my chart</a></p>
    </section>
  </div>
</main>

<?php
  include 'footer.php';
?>
