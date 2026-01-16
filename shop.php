<?php
  require 'includes/current-user.inc.php';
  require_once 'includes/guide.inc.php';
  require_once 'includes/crystals.inc.php';

  include 'header.php';
  include 'nav.php';

  $element = $_GET["element"] ?? "";
  if (!in_array($element, ELEMENTS, true)) {
    $element = "";
  }

  $goal = $chart ? guideMonthlyGoal($chart, $today) : null;
  $ranked = $chart ? recommendCrystals($chart, $goal["element"]) : [];
  if (!$chart) {
    foreach (CRYSTALS as $id => $crystal) {
      $ranked[] = ["id" => $id, "crystal" => $crystal, "score" => 0, "reasons" => []];
    }
  }
  $picks = array_slice(array_filter($ranked, function ($item) { return $item["score"] > 0; }), 0, 3);
  $catalog = $element === "" ? $ranked : array_values(array_filter($ranked, function ($item) use ($element) {
    return $item["crystal"]["element"] === $element;
  }));
?>

  <header class="page-banner" style="background-image: url('img/site/mission-shop.jpg');">
    <div class="container">
      <span class="eyebrow">Crystals for your spiritual journey</span>
      <h1>Your Crystal Shop</h1>
      <p>
        <?php if ($chart) { ?>
          Every crystal is ranked for you, <?php echo e($user["usersName"]); ?>: crystals tied to your
          Sun in <?php echo e($chart["placements"]["Sun"]["sign"]); ?>, your Moon in <?php echo e($chart["placements"]["Moon"]["sign"]); ?><?php
          echo isset($chart["placements"]["Ascendant"]) ? ", and your Ascendant in " . e($chart["placements"]["Ascendant"]["sign"]) : ""; ?>
          come first, along with crystals that bring in <?php echo e(strtolower($goal["element"])); ?> for this month's goal,
          "<?php echo e(rtrim($goal["title"], ".")); ?>."
        <?php } else { ?>
          <a href="birth-details.php">Add your birth details</a> to see which crystals fit your chart.
        <?php } ?>
      </p>
    </div>
  </header>

  <main class="container">
    <?php if ($picks) { ?>
      <section class="section">
        <div class="section-head">
          <span class="eyebrow">Picked for You</span>
          <h2>Your top three</h2>
        </div>
        <div class="product-grid product-grid-3">
          <?php foreach ($picks as $item) { echo crystalCardHtml($item); } ?>
        </div>
      </section>
    <?php } ?>

    <section class="section" id="all-crystals" style="padding-top: <?php echo $picks ? "0" : "72px"; ?>;">
      <div class="section-head">
        <span class="eyebrow">The Collection</span>
        <h2>All Crystals</h2>
      </div>
      <nav class="element-filter" aria-label="Filter by element">
        <a href="shop.php#all-crystals" class="<?php echo $element === "" ? "active" : ""; ?>">All</a>
        <?php foreach (ELEMENTS as $el) { ?>
          <a href="shop.php?element=<?php echo $el; ?>#all-crystals" class="<?php echo $element === $el ? "active" : ""; ?>"><?php echo $el; ?></a>
        <?php } ?>
      </nav>
      <div class="product-grid">
        <?php foreach ($catalog as $item) { echo crystalCardHtml($item); } ?>
      </div>
    </section>
  </main>

<?php
  include 'footer.php';
?>
