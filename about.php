<?php
  require 'includes/current-user.inc.php';
  require_once 'includes/guide.inc.php';
  require_once 'includes/chart-wheel.inc.php';

  include 'header.php';
  include 'nav.php';

  $birthTime = userBirthTime($user);
  $birthDateText = $chart ? (new DateTimeImmutable(substr($user["usersBirthDate"], 0, 10)))->format("F j, Y") : "";
  $birthTimeText = $birthTime ? (DateTimeImmutable::createFromFormat("H:i", $birthTime))->format("g:i A") : "";

  $bigThree = [
    "Sun" => ["img" => "img/about/sun.jpg", "alt" => "The sun setting over the sea"],
    "Moon" => ["img" => "img/about/moon.jpg", "alt" => "The moon in a violet sky"],
    "Ascendant" => ["img" => "img/about/mask.jpg", "alt" => "A sculpted face behind a mask"],
  ];
  $signKey = ["Sun" => "sun", "Moon" => "moon", "Ascendant" => "rising"];
?>

  <section class="sky">
    <div class="container">
      <?php if (isset($_GET["updated"])) { ?>
        <div class="notice notice-success"><p>Your birth details were updated, and your chart and guide have been recalculated.</p></div>
      <?php } ?>
      <?php if ($chart) { ?>
        <span class="eyebrow">Your Birth Chart</span>
        <h1>
          This was the sky<?php echo $chart["placeKnown"] ? " above " . e($user["usersPlace"]) : ""; ?>
          on <?php echo e($birthDateText); ?><?php echo $birthTime ? " at exactly " . e($birthTimeText) : ""; ?>!
        </h1>
        <div class="wheel-wrap">
          <?php echo renderChartWheel($chart); ?>
        </div>
      <?php } else { ?>
        <h1>Your Birth Chart</h1>
        <div class="notice">
          <p>We couldn't read your birth details. <a href="birth-details.php">Add your birth date, time, and city</a> to see your chart.</p>
        </div>
      <?php } ?>
    </div>
  </section>

  <main>
    <section class="section">
      <div class="container container-narrow" style="text-align: center;">
        <span class="eyebrow">How we chose your guide</span>
        <p class="lead">Crystal builds an astrological understanding of its users to the very minute they
          were born. It allows us to create a guide based on their weaknesses by complementing their strengths.
          Our research has shown that people struggle to begin their spiritual journey, because they do not know where
          to start. We give them that start, and help guide them continuously.
          Our guide is built with an overall monthly goal, with weekly aspects to focus on and daily tasks to help
          reach their month's goal. We give you the option to supplement your spiritual journey
          by customizing what crystals you should purchase.</p>
        <a class="btn btn-outline" href="birth-details.php">Update your birth details</a>
      </div>
    </section>

    <?php if ($chart) { ?>
    <section class="section section-mist">
      <div class="container">
        <div class="section-head">
          <span class="eyebrow">Your Big Three</span>
          <h2>Sun, Moon &amp; Ascendant</h2>
        </div>
        <div class="big-three-cards">
          <?php foreach ($bigThree as $body => $pic) { ?>
            <article class="card placement-card">
              <img src="<?php echo $pic["img"]; ?>" class="round-photo" alt="<?php echo e($pic["alt"]); ?>" loading="lazy" />
              <?php if (isset($chart["placements"][$body])) { $p = $chart["placements"][$body]; ?>
                <h3><?php echo e($body . " in " . $p["sign"]); ?></h3>
                <p><?php echo e(BODY_INFO[$body]["intro"] . " " . SIGN_INFO[$p["sign"]][$signKey[$body]]); ?></p>
                <?php if (cuspPhrase($p) !== null) { ?>
                  <p class="note">Your <?php echo e(strtolower($body)); ?> is right <?php echo e(cuspPhrase($p)); ?>, so you may recognize yourself in both signs.</p>
                <?php } ?>
                <?php if ($p["uncertain"]) { ?>
                  <p class="note">Without your exact birth <?php echo $chart["timeKnown"] ? "city" : "time"; ?>, we can't be sure of this sign: your <?php echo e(strtolower($body)); ?> changed signs that day. <a href="birth-details.php">Add it</a> to be sure.</p>
                <?php } ?>
              <?php } else { ?>
                <h3><?php echo e($body); ?></h3>
                <p><?php echo e(BODY_INFO[$body]["intro"]); ?></p>
                <p class="note">Your Ascendant needs your exact birth time and city. <a href="birth-details.php">Add them</a> to find yours.</p>
              <?php } ?>
            </article>
          <?php } ?>
        </div>
      </div>
    </section>

    <section class="section chart-table-section">
      <div class="container container-narrow">
        <div class="section-head">
          <span class="eyebrow">Planets</span>
          <h2>The Rest of Your Chart</h2>
        </div>
        <div class="card">
          <table class="chart-table">
            <thead>
              <tr><th scope="col">Placement</th><th scope="col">Sign</th><th scope="col">Degree</th><th scope="col" class="col-meaning">What it shows</th></tr>
            </thead>
            <tbody>
              <?php foreach ($chart["placements"] as $body => $p) { if (isset($bigThree[$body])) continue; ?>
                <tr>
                  <td><span class="glyph" aria-hidden="true"><?php echo e(textGlyph(BODY_INFO[$body]["glyph"])); ?></span> <?php echo e($body); ?></td>
                  <td><span class="glyph" aria-hidden="true"><?php echo e(textGlyph(SIGN_INFO[$p["sign"]]["glyph"])); ?></span> <?php echo e($p["sign"]); ?><?php echo $p["uncertain"] ? " *" : ""; ?>
                    <?php if (cuspPhrase($p) !== null) { ?><br><span class="cusp-note"><?php echo e(ucfirst(cuspPhrase($p))); ?></span><?php } ?></td>
                  <td><?php echo e(formatDegree($p["degree"])); ?></td>
                  <td class="col-meaning"><?php echo e(ucfirst(BODY_INFO[$body]["meaning"])); ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <?php
          $anyUncertain = false;
          foreach ($chart["placements"] as $body => $p) {
            if (!isset($bigThree[$body]) && $p["uncertain"]) $anyUncertain = true;
          }
          if ($anyUncertain) {
        ?>
          <p class="note">* This planet changed signs around your birth date. Add your exact birth time and city to be sure.</p>
        <?php } ?>
        <p class="note" style="text-align: center; margin-top: 18px;">Positions are calculated for the tropical zodiac from your birth date, time, and place.</p>
      </div>
    </section>
    <?php } ?>
  </main>

<?php
  include 'footer.php';
?>
