<?php
  include 'header.php';
  $showSiteLinks = true;
  include 'nav.php';
?>

    <section class="hero">
      <div class="container">
        <div class="hero-content">
          <span class="eyebrow">Astrology &middot; Spiritual guide &middot; Crystals</span>
          <h1>Crystal</h1>
          <?php if (isset($_SESSION["username"])) { ?>
            <p class="hero-tagline">Hello there <?php echo htmlspecialchars($_SESSION["username"], ENT_QUOTES); ?>!<br>Your guide for today is ready.</p>
            <div class="btn-row">
              <a class="btn btn-primary" href="profile.php">See your guide</a>
              <a class="btn btn-ghost" href="shop.php">Your crystal shop</a>
            </div>
          <?php } else { ?>
            <p class="hero-tagline">Bringing you closer to yourself through spirituality.</p>
            <div class="btn-row">
              <a class="btn btn-primary" href="signup.php">Get your guide</a>
              <a class="btn btn-ghost" href="#mission">Learn more</a>
            </div>
          <?php } ?>
        </div>
      </div>
      <a class="hero-scroll" href="#mission">Scroll down to find out more</a>
    </section>

    <main>
      <!-- our mission -->
      <section class="section mission" id="mission">
        <div class="container">
          <div class="section-head">
            <span class="eyebrow">Our Mission</span>
            <h2>Spirituality made personal</h2>
          </div>

          <!-- first: spread spirituality -->
          <div class="feature">
            <div class="feature-media">
              <img src="img/site/mission-spread.jpg" alt="A person in a yoga pose silhouetted against a sunset over still water" loading="lazy" />
            </div>
            <div class="feature-text">
              <span class="eyebrow">Spread Spirituality</span>
              <h3>What is spirituality?</h3>
              <p>Spirituality is an individual practice that focuses on living in the present.
                This individual practice aims to further one's understanding of themselves without
                reassurance. Spirituality relates to the process of developing beliefs around
                the meaning of life and connection with others, without any set spiritual values.</p>
            </div>
          </div>

          <!-- second: individualized guide -->
          <div class="feature feature-reverse">
            <div class="feature-media">
              <img src="img/site/mission-guide.jpg" alt="The Milky Way over a mountain ridge at night" loading="lazy" />
            </div>
            <div class="feature-text">
              <span class="eyebrow">The Right Guide for You</span>
              <h3>Individualized guide.</h3>
              <p>We aim to customize a spiritual guide for every user through astrology so that each person can focus on the aspects of spirituality that best fit them. Spirituality is related to the soul and astrology is related to planets. Astrology deals with observing the movements of the planets and making calculations related to the events that are yet to happen. Astrology offers knowledge that we can use to build guides for spiritual practices and devotions.</p>
            </div>
          </div>

          <!-- third: shop all things spiritual -->
          <div class="feature">
            <div class="feature-media">
              <img src="img/site/mission-shop.jpg" alt="The inside of an amethyst geode" loading="lazy" />
            </div>
            <div class="feature-text">
              <span class="eyebrow">Your Crystal Shop</span>
              <h3>Crystals for your spiritual journey.</h3>
              <p>In the Middle Ages, people thought that crystals would bring a spiritual presence. People had this hunger to have something physical that embodies faith and spirituality. People still have that hunger now, and that’s why crystals continue to grow in popularity. Crystals, in a way, fulfill a spiritual need for some people. Some people go to church, some do yoga, and others collect and meditate with crystals. We want our users to be able to obtain the right crystals for their spiritual journey and needs. With dozens of different crystals to choose from, we will help make the right decision for you.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- how it works -->
      <section class="section section-mist">
        <div class="container">
          <div class="section-head">
            <span class="eyebrow">How Crystal Works</span>
            <h2>Your guide in three steps</h2>
          </div>
          <div class="steps">
            <div class="card step">
              <h3>Tell us when you were born</h3>
              <p>Your birth date, time, and city are all we need to map the sky at the minute you were born.</p>
            </div>
            <div class="card step">
              <h3>Meet your chart</h3>
              <p>See your Sun, Moon, Ascendant, and every planet, and what each one says about you.</p>
            </div>
            <div class="card step">
              <h3>Follow your guide</h3>
              <p>A monthly goal, a weekly focus, and daily ideas, plus the crystals that fit your journey.</p>
            </div>
          </div>
          <?php if (!isset($_SESSION["username"])) { ?>
            <div class="section-actions"><a class="btn btn-primary" href="signup.php">Get your guide</a></div>
          <?php } ?>
        </div>
      </section>

      <!-- about us -->
      <section class="section about" id="about">
        <div class="container team">
          <div class="team-photos">
            <img src="img/index/team1.jpg" alt="Team picture" loading="lazy" />
            <img src="img/index/team2.jpg" alt="Team picture" loading="lazy" />
          </div>
          <div>
            <span class="eyebrow">About Us</span>
            <h2>How we started.</h2>
            <p>Co-Founder Andrea Dobson began her spiritual journey at the age of 16. It started small
              with purchasing crystals to help create positive energy. She then got into Astrology and was
              interested in what the stars could tell of her personality and realized that she had been
              fighting against her own character to be someone she was not truly. It created guidance for
              her in a fast paced world, where it is quick to feel left behind.</p>
            <p>With spirituality being such a positive force in her life, she realized that it was difficult
              to grow her spirituality because there was not a dedicated source to learn about. Spirituality
              is about understanding deeper of oneself. It should not take hours of self research to further
              one's spirituality. And that is what she set out for.</p>
            <p>All of the current applications on spirituality are astrology heavy and do not encompass all of
              spirituality. We aim to give each person interested in spirituality their own personalized guide
              to spirituality, so that they may quickly be on the path to understanding themselves more clearly.</p>
          </div>
        </div>
      </section>

      <!-- contact -->
      <section class="section section-mist contact-section" id="contact">
        <div class="container">
          <div class="card contact">
            <div class="contact-text">
              <span class="eyebrow">Contact</span>
              <h2>Say hello</h2>
              <p class="muted">Got something you want to talk about? Email me and I promise to get back to you as soon as I can.</p>
              <dl>
                <dt>Email</dt>
                <dd><a href="mailto:idobson3@gatech.edu">idobson3@gatech.edu</a></dd>
                <dt>Address</dt>
                <dd>220 Ferst Dr NW<br>30318 Atlanta, GEORGIA</dd>
              </dl>
            </div>
            <img src="img/site/contact.jpg" alt="A hand holding a crystal ball that reflects a field of flowers" loading="lazy" />
          </div>
        </div>
      </section>
    </main>
<?php
  include 'footer.php';
?>
