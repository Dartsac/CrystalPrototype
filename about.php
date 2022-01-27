<?php
  include 'header.php';
?>

<header class="header" id="nav-bar">
    <a href="index.php"><img src="img/logo.png" class="logo"/></a>
    <div id="nav">
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

  <main class="container">
    <div class="const">
      <?php
        if (isset($_SESSION["username"])) {
          echo "<img src='img/" . $_SESSION["username"] .  "/const.gif'style='width: 100%; border-radius: 50%; margin-top: 70px;'/>";
          echo "<h3 style='text-align:center'> These were the constellations above ". $_SESSION["user-place"] . " on " . $_SESSION["user-day"] . " at exactly " . $_SESSION["user-time"] . "!</h3>";
        }
       ?>
       <br>
       <br>
       <br>

       <p style="text-align: center">Crystal builds an astrologial understanding of its users to the very minute they
         were born. It allows us to create a guide based on their weaknesses by complimenting on their strengths.
         Our research has shown that people stuggle to begin their spiritual journey, becuase they do not know where
         to start. We give them that start, and help guide them continuously.
         Our guide is built with an overall monthly goal, with weekly aspects to focus on and daily tasks to help
         reach their month's goal. We give you the option to supplement your spiritual journey
         by customizing what crystals you should purchase.
       </p>

       <br>
       <br>
       <br>

    </div>


    <section>
      <!-- first:SUN -->
      <div class="row no-gutters" style="height: 555px;">

        <div class="col">
          <img src="img/about/sun.jpg" style="border-radius: 50%; position: relative; width: 100%;" alt="Spread" />
        </div>

        <?php
            if ($_SESSION["username"] === "SirFawkes") {
         ?>

        <div class="col">
          <div class="extra-gutters">
            <h3>Sun in Leo</h3>
            <p> The sun determines your ego, identity, and "role" in life. It's the core
              of who you are, and is the sign you're most likely to already know. Your
              Sun is in Leo, meaning you are fundamentally bold and proud. You love attention
              and to be cared for. You pay it back in kind with your charm and your ability
              to make people feel better when they're blue. You know what your skills are and
              how to use them.
            </p>
          </div>
        </div>

      </div>

      <?php
        } elseif ($_SESSION["username"] === "andiebeca") {
       ?>
       <div class="col">
           <div class="extra-gutters">
             <h3>Sun in Aquarius</h3>
             <p> The sun determines your ego, identity, and "role" in life. It's the core of
               who you are, and is the sign you're most likely to already know. Your Sun is in
               Aquarius, meaning you are fundamentally unconventional and anti. Comfortable dissenting
               or straying from the norm, your intellectual talent of abstraction is oriented
               towards pushing the boundaries of what seems reasonable. You carry a lot on your shoulders
               and have need to fight for the underdog.
           </div>
       </div>

       <?php
          }
       ?>


      <!-- second: MOON -->
      <div class="row no-gutters" style="height: 555px;">

        <?php
            if ($_SESSION["username"] === "SirFawkes") {
         ?>
        <div class="col">
            <div class="extra-gutters right-aligned">
              <h3>Moon in Gemini</h3>
              <p> The moon rules your emotions, moods, and feelings. This is likely the sign you
                 most think of yourself as, since it reflects your personality when you're alone
                 or deeply comfortable. Your Moon is in Gemini, meaning your emotional self is often
                 restless and unsettled. You're extremely adaptable, which sometimes makes you feel
                 pulled in too many directions. You are easily bored and need to feel like you are
                 free to be creative and create meaning, which can make you feel like you're in a
                 constant identity crisis.
              </p>
            </div>
        </div>

        <?php
          } elseif ($_SESSION["username"] === "andiebeca") {
         ?>
         <div class="col">
             <div class="extra-gutters">
               <h3>Moon in Cancer</h3>
               <p> The moon rules your emotions, moods, and feelings. This is likely the sign you most
                 think of yourself as, since it reflects your personality when you're alone or deeply
                 comfortable. Your Moon is in Cancer, meaning your emotional self is sensitive, thoughtful,
                 and empathetic. You have a tendency to feel like a martyr, and secretly fear being
                 abandoned by those you love. You often have trouble letting things go and feel like an
                 emotional wreck.
               </p>
             </div>
         </div>

         <?php
            }
         ?>

        <div class="col">
          <img src="img/about/moon.jpg" style="border-radius: 50%; position: relative; width: 100%;" alt="Individual" />
        </div>

      </div>
      <!-- third: Ascendant-->
      <div class="row no-gutters" style="height: 555px;">

        <div class="col">
          <img src="img/about/mask.jpg" style="border-radius: 50%; position: relative; width: 100%;" alt="Aesthetic" />
        </div>

        <?php
            if ($_SESSION["username"] === "SirFawkes") {
         ?>
        <div class="col">
            <div class="extra-gutters">
              <h3>Ascendant in Scorpio</h3>
              <p> Your ascendant is the "mask" you present to people. It can be seen in your personal
                style and how you come off to people when you first meet. Some say it becomes less
                relevant as you get older. It changes every two hours, so if it doesn't make sense,
                reconfirm your birth time to be sure. Your Ascendant is in Scorpio, meaning you come
                across as passionate, incisive, cunning, strategic, and perceptive. Your intense and
                tenacious drive comes off as intimidating and powerful if not malicious or aggressive.
              </p>
            </div>
        </div>

        <?php
          } elseif ($_SESSION["username"] === "andiebeca") {
         ?>
         <div class="col">
             <div class="extra-gutters">
               <h3>Ascendant in Gemini</h3>
               <p> Your ascendant is the "mask" you present to people. It can be seen in your personal
                 style and how you come off to people when you first meet. Some say it becomes less
                 relevant as you get older. It changes every two hours, so if it doesn't make sense,
                 reconfirm your birth time to be sure. Your Ascendant is in Gemini, meaning you come
                 across as quick-witted, chatty, eclectic, and fun, though somewhat inconsistent.
                 In conversation, you seem probing and curious.
               </p>
             </div>
         </div>

         <?php
            }
         ?>


      </div>
    </section>
