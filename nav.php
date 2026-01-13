<?php
  // Top navigation bar. Set $showSiteLinks = true before including it to show
  // the links to the home page sections (as on the home, log in and sign up pages).
  $showSiteLinks = $showSiteLinks ?? false;
  $currentPage = basename($_SERVER["SCRIPT_NAME"] ?? "");
  $navClass = function ($page) use ($currentPage) {
    return $currentPage === $page ? "nav-link active" : "nav-link";
  };
?>
<header class="site-header" id="nav-bar">
    <a href="index.php" class="brand"><img src="img/logo.png" alt="Crystal home"/><span>Crystal</span></a>
    <nav id="nav" aria-label="Main">
      <?php if ($showSiteLinks) { ?>
        <a href="index.php#mission" class="nav-link nav-site">OUR MISSION</a>
        <a href="index.php#about" class="nav-link nav-site">ABOUT US</a>
        <a href="index.php#contact" class="nav-link nav-site">CONTACT</a>
      <?php } ?>
      <?php if (isset($_SESSION["userid"])) { ?>
        <a href="profile.php" class="<?php echo $navClass("profile.php"); ?>">PROFILE</a>
        <a href="about.php" class="<?php echo $navClass("about.php"); ?>">MY CHART</a>
        <a href="shop.php" class="<?php echo $navClass("shop.php"); ?>">SHOP</a>
        <a href="includes/logout.inc.php" class="nav-link nav-strong">LOG OUT</a>
      <?php } else { ?>
        <a href="login.php" class="nav-link nav-strong">LOG IN</a>
      <?php } ?>
    </nav>
</header>
