<?php
  include 'header.php';
  require_once 'includes/photo-credits.inc.php';
  $showSiteLinks = true;
  include 'nav.php';
?>

  <main class="container container-narrow page-top">
    <section class="section">
      <div class="section-head">
        <span class="eyebrow">Thank you</span>
        <h1 style="font-size: clamp(40px, 5vw, 56px);">Photo Credits</h1>
        <p class="muted">These photographers share their work under open licenses. The photos were resized and cropped for this site.</p>
      </div>
      <div class="card">
        <table class="chart-table credits-table">
          <thead>
            <tr><th scope="col">Photo</th><th scope="col">Used for</th><th scope="col">Credit</th></tr>
          </thead>
          <tbody>
            <?php foreach (PHOTO_CREDITS as [$where, $file, $creator, $license, $licenseUrl, $source]) { ?>
              <tr>
                <td><img class="credits-thumb" src="<?php echo htmlspecialchars($file, ENT_QUOTES); ?>" alt="" loading="lazy"></td>
                <td><?php echo htmlspecialchars($where, ENT_QUOTES); ?></td>
                <td>
                  <a href="<?php echo htmlspecialchars($source, ENT_QUOTES); ?>" rel="noopener">Photo</a>
                  by <?php echo htmlspecialchars($creator, ENT_QUOTES); ?>,
                  <a href="<?php echo htmlspecialchars($licenseUrl, ENT_QUOTES); ?>" rel="license noopener"><?php echo htmlspecialchars($license, ENT_QUOTES); ?></a>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

<?php
  include 'footer.php';
?>
