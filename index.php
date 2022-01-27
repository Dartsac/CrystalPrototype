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
              echo "<a href='profile.php' style='margin-left:25px; text-decoration: none;color: #112637;'> PROFILE</a>";
              echo "<a href='includes/logout.inc.php' style='margin-left:25px; text-decoration: none;font-weight: 600; color: #112637;'> LOG OUT</a>";
            } else {
              echo "<a href='login.php' style='margin-left:25px; text-decoration: none;font-weight: 600; color: #112637;'> LOG IN</a>";
            }
          ?>
        </div>
    </header>

    <header class="row">
      <!-- left side -->
      <section class="col left-col">
        <div class="top-container">
          <h1>Crystal</h1>
          <?php
            if (isset($_SESSION["username"])) {
              echo "<p> Hello there " . $_SESSION["username"] . "!</p>";
            } else {
              echo "<p> Bringing you<br>
                closer to yourself<br>
                through spirituality
              </p>";
            }
           ?>
        </div>
      </section>

      <!-- right side -->
      <section class="col right-col">
        <div class="top-container">
          <img src="img/index/holdingCrystal.jpeg" class="holding-img" alt="Crystal" />
        </div>
      </section>
    </header>

    <main class="container">
      <p class="scroll">Scroll down to find out more about Crystal</p>
      <!-- our mission -->
      <section class="mission" id = "mission">
        <h2>Our Mission</h2>
        <!-- first:spread spirituality -->
        <div class="row no-gutters" style="height: 555px;">

          <div class="col">
            <h4>Spread Spirituality</h4>
            <img src="img/index/spread.jpeg" class="mission-img" alt="Spread" />
          </div>

          <div class="col">
            <div class="extra-gutters">
              <h3>What is spirituality?</h3>
              <p> Spirituality is an individual practice that focuses on living in the present.
                  This individual practice aims to further one's understanding of themselves without
                  reassurance. Spirituality relates to the process of developing beliefs around
                  the meaning of life and connection with others, without any set spiritual values.
              </p>
            </div>
          </div>

        </div>
        <!-- second: individualized guide -->
        <div class="row no-gutters" style="height: 555px;">

          <div class="col">
              <div class="extra-gutters right-aligned">
                <h3>Individualized guide.</h3>
                <p> We aim to customize a spiritual guide for every user through astrology so that each person can focus on the aspects of spirituality that best fit them. Spirituality is related to the soul and astrology is related to planets. Astrology deals with observing the movements of the planets and making calculations related to the events that are yet to happen. Astrology offers knowledge that we can use to build guides for spiritual practices and devotions.
                </p>
              </div>
          </div>

          <div class="col">
            <h4 class="right">The Right Guide for You</h4>
            <img src="img/index/indiv.jpeg" class="mission-img" alt="Individual" />
          </div>

        </div>
        <!-- third: shop all things spiritual -->
        <div class="row no-gutters" style="height: 555px;">

          <div class="col">
            <h4>Your Crystal Shop</h4>
            <img src="img/index/aesthetic3.jpeg" class="mission-img" alt="Aesthetic" />
          </div>

          <div class="col">
              <div class="extra-gutters">
                <h3>Crystals for your sprirtual journey.</h3>
                <p> In the Middle Ages, people thought that crystals would bring a spiritual presence. People had this hunger to have something physical that embodies faith and spirituality. People still have that hunger now, and that’s why crystals continue to grow in popularity. Crystals, in a way, fulfill a spiritual need for some people. Some people go to church, some do yoga, and others collect and meditate with crystals. We want our users to be able to obtain the right crystals for their spiritual journey and needs. With dozens of different crystals to choose from, we will help make the right decision for you.
                </p>
              </div>
          </div>

        </div>
      </section>

      <!-- about us -->
      <section class="about" id="about">
        <h2>About Us</h2>
        <div class="row">

          <div class="col-md-6">
            <img src="img/index/team1.jpg" class="img-margin" alt="Team picture" />
            <img src="img/index/team2.jpg" class="img-margin" alt="Team picture" />
          </div>

          <div class="col">
            <h3>How <br>we started.</h3>
            <p> Co-Founder Andrea Dobson began her spiritual journey at <br>
                the age of 16. It started small with purchasing crystals <br>
                to help create positve energy. She then got into Astrology <br>
                and and was interested in what the stars could tell of her <br>
                personality and realized that she had been fighting against <br>
                her own character to be someone she was not truly. It created <br>
                guidence for her in a fast paced world, where it is quick <br>
                to feel left behind.
                <br><br>
                With spirituality being such a positive force in her life,<br>
                she realized that it was difficult to grow her spirituality<br>
                because there was not a dedicated source to learn about.<br>
                Spirituality is about understanding deeper of oneself. It <br>
                should not take hours of self research to further one's<br>
                spirituality. And that is what she set out for.<br>
                <br>
                All of the current applications on spirituality are astrology<br>
                heavy and do not encompass all of spirituality. We aim to<br>
                give each person interested in spirituality their own personized<br>
                guide to spirituality, so that they may quickly be on the path<br>
                to understanding themselves more clearly.<br>
            </p>
          </div>

        </div>
      </section>

      <!-- contact -->
      <section class="contact" id="contact">
        <h2>Contact</h2>
        <div class="row">

          <div class="col">
            <p>Got something you want to talk about?
            <br>Email me and I promise to get back to you as soon as I can.</p>
            <p>Email: idobson3@gatech.edu </p>

            <p> 220 Ferst Dr NW<br>
                30318 Atlanta,<br>
                GEORGIA
            </p>
          </div>

          <div class="col">
            <img src="img/index/contact1.jpg" style="width: inherit; height: inherit;" alt="" />
          </div>

          <div class="col">
            <img src="img/index/contact2.jpg" style="width: inherit; height: inherit;" alt="" />
          </div>

        </div>
      </section>
    </main>
<?php
  include 'footer.php';
?>
