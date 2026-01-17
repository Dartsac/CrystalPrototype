<?php
  require 'includes/current-user.inc.php';
  require_once 'includes/guide.inc.php';
  require_once 'includes/crystals.inc.php';

  include 'header.php';
  include 'nav.php';

  $guide = $chart ? buildGuide($chart, (int) $user["usersId"], $today) : null;
  $featured = $guide ? array_slice(recommendCrystals($chart, $guide["monthly"]["element"]), 0, 3) : [];

  $banner = userImage($user, "banner.gif", "img/site/mission-guide.jpg");
  $photo = userImage($user, "profile.png", null);

  // three different decorative pictures for the guide cards
  $ran_num = range(1, 10);
  shuffle($ran_num);
  [$a, $b, $c] = array_slice($ran_num, 0, 3);
?>

  <!--cover area -->
  <div class="profile-hero" style="background-image: url('<?php echo e($banner); ?>');">
    <a href="about.php" id="about-guide" class="btn btn-ghost">How we chose your guide</a>
  </div>

  <div class="profile-head container">
    <?php if ($photo !== null) { ?>
      <img class="profile-photo" src="<?php echo e($photo); ?>" alt="<?php echo e($user["usersName"]); ?>"/>
    <?php } else { ?>
      <div class="profile-photo profile-initial" aria-hidden="true"><?php echo e(strtoupper(substr($user["usersName"], 0, 1))); ?></div>
    <?php } ?>
    <h1>Hello there <?php echo e($user["usersName"]); ?>!</h1>
    <?php if ($chart) { ?>
      <div class="big-three">
        <?php foreach (["Sun", "Moon", "Ascendant"] as $body) {
          if (!isset($chart["placements"][$body])) continue; ?>
          <span class="chip" title="<?php echo e($body); ?>"><span class="glyph" aria-hidden="true"><?php echo e(textGlyph(BODY_INFO[$body]["glyph"])); ?></span><?php echo e($body . " in " . $chart["placements"][$body]["sign"]); ?></span>
        <?php } ?>
      </div>
    <?php } ?>
  </div>

  <main class="container">

    <?php if (!$chart) { ?>
      <div class="notice">
        <p>We couldn't read your birth details, so we can't build your guide yet.
          <a href="birth-details.php">Add your birth date, time, and city</a> to get started.</p>
      </div>
    <?php } elseif (!$chart["timeKnown"] || !$chart["placeKnown"]) { ?>
      <div class="notice">
        <p>Your guide is missing your <?php echo !$chart["timeKnown"] && !$chart["placeKnown"] ? "birth time and city" : (!$chart["timeKnown"] ? "birth time" : "birth city"); ?>,
          so we can't place your Ascendant<?php echo $chart["placements"]["Moon"]["uncertain"] ? " or be sure of your Moon sign" : ""; ?>.
          <a href="birth-details.php">Update your birth details</a> for a complete guide.</p>
      </div>
    <?php } ?>

    <?php if ($guide) { $monthly = $guide["monthly"]; $weekly = $guide["weekly"]; $daily = $guide["daily"]; ?>
    <div class="guide">

      <!--Monthly Goal-->
      <section class="card guide-card guide-card-wide">
        <img class="guide-art" src="img/stock/<?php echo $a; ?>.png" alt="" />
        <div>
          <span class="eyebrow">This Month's Goal &middot; <?php echo e($today->format("F")); ?></span>
          <h2><?php echo e($monthly["title"]); ?></h2>
          <p><?php echo e($monthly["body"]); ?></p>
          <p class="note"><?php echo e($monthly["why"]); ?></p>
        </div>
      </section>

      <!-- This Week's Plan -->
      <section class="card guide-card">
        <img class="guide-art guide-art-small" src="img/stock/<?php echo $b; ?>.png" alt="" />
        <div class="card-head">
          <span class="eyebrow">The Week's Plan</span>
          <h3><?php echo e($weekly["title"]); ?></h3>
        </div>
        <p class="note"><?php echo e($weekly["source"]); ?></p>
        <p><?php echo e($weekly["insight"]); ?></p>
        <p><?php echo e($weekly["practice"]); ?></p>
      </section>

      <!--daily guide-->
      <section class="card guide-card">
        <img class="guide-art guide-art-small" src="img/stock/<?php echo $c; ?>.png" alt="" />
        <div class="card-head">
          <span class="eyebrow">Today's Guide &middot; <?php echo e($today->format("l")); ?></span>
          <h3><?php echo e($daily["body"] . " in " . $daily["sign"]); ?></h3>
        </div>
        <p class="note"><?php echo e($daily["moonText"]); ?></p>
        <p><?php echo e($daily["text"]); ?></p>
        <ul class="ideas" aria-label="Ideas">
          <?php foreach ($daily["ideas"] as $idea) { ?><li><?php echo e($idea); ?></li><?php } ?>
        </ul>
      </section>

    </div>
    <?php } ?>

    <?php if ($featured) { ?>
    <!-- featured crystals -->
    <section class="section">
      <div class="section-head">
        <span class="eyebrow">Featured Crystal Shop</span>
        <h2>Crystals for you this month</h2>
      </div>
      <div class="product-grid product-grid-3">
        <?php foreach ($featured as $item) { echo crystalCardHtml($item, false); } ?>
      </div>
      <div class="section-actions"><a class="btn btn-outline" href="shop.php">Visit your crystal shop</a></div>
    </section>
    <?php } ?>

  </main>

<?php
  include 'footer.php';
?>
