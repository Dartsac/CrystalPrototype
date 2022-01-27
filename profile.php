<?php
  include 'header.php';
?>
<!--
$_SESSION["userid"] = $userNameExists["usersId"];
$_SESSION["username"] = $userNameExists["usersName"];
$_SESSION["user-day"] = $userNameExists["usersBirthDate"];
$_SESSION["user-time"] = $userNameExists["usersBirthTime"];
$_SESSION["user-place"] = $userNameExists["usersPlace"];
-->
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

<!--cover area -->
  <div style="width: 80%; margin:auto;">
    <div style="text-align: center; position: relative;" >
      <a href='about.php' id="about-guide"> How we chose your guide.</a>

      <?php
      if (isset($_SESSION["username"])) {
        echo "<img class='stars' src='img/" . $_SESSION["username"] .  "/banner.gif'style='width: 100%;'/>";
      }
      ?>

      <img class="twinkling" src="img/twinkling.png" style="width:100%;"/>
      <img class="clouds" src="img/clouds.png" style="width:100%;"/>
    </div>
    <div style="text-align: center; position: relative;" >
      <?php
      if (isset($_SESSION["username"])) {
        echo "<img src='img/" . $_SESSION["username"] .  "/profile.png'style='width: 11.5vw; height: 11.5vw; object-fit: cover; margin-top: -250px; border-radius: 50%; border: solid 4px white;'/>";
      }
      ?>
    </div>
  </div>
  <?php
  echo "<p style='text-align: center; font-size: 1vw;'> Hello there " . $_SESSION["username"] . "!</p>";
  ?>
  <br>
  <br>

  <!--Monthly Goal-->
  <main class="container">
    <?php
    if ($_SESSION["username"] === "SirFawkes") {
    ?>
    <div class="daily-guide">
        <h2> This Month's Goal </h2>
        <h3> Build deeper relationships with friends. </h3>
        <p class="last"> Friendship and has shown to make up 70% of a person’s happiness.
            Having strong social relationships can have effects on your overall health,
            including building a stronger immune system, and living a longer,
            more fulfilling life.
        </p>
    </div>
  <?php }elseif ($_SESSION["username"] === "andiebeca") {
   ?>
   <div class="daily-guide">
       <h2> This Month's Goal </h2>
       <h3> Build a meditation and/or reflection habit. </h3>
       <p class="last"> Only do 5 minutes a day. Meditation is a great way to consciously and unconsciously get in touch with your spiritual self. By sitting in a comfortable position and focusing on your breath you can allow your mind to step away from the troubles of the physical world and drift into a realm of relaxation.
       </p>
   </div>
       <?php }
       ?>

    <?php
    $ran_num = range(1,10);
    shuffle($ran_num);
    $array = array_slice($ran_num, 0, 3);
    $a = $ran_num[0];
    $b = $ran_num[1];
    $c = $ran_num[2];
     ?>
    <!-- first random picture -->
    <div style="margin-top: 60px; margin-bottom: 10px;">
          <div class="row">
            <div class="col-2">
              <?php
              if (isset($_SESSION["username"])) {
                echo "<img src='img/stock/" . $a .  ".png'style='width: 9.5vw; height: 9.5vw; object-fit: cover; border-radius: 50%; border: solid 4px #fff; margin-left: ".rand(1,14)."vw;'/>";
              }
               ?>
            </div>
          </div>
    </div>
  <!-- This Week's Plan -->
  <?php
  if ($_SESSION["username"] === "SirFawkes") {
  ?>
    <div class="daily-guide">
        <h2> The Week's Plan </h2>
        <h3> Listen up. </h3>
        <p>
          Leos love being in charge which can lead them to seem smothering to friends.
          It is natural for you to dominate a convorsation.
        </p>
        <p class="last">
          Focus this week on noticing when you have interupted someone, and let that person finish their sentence.
          True listening requires a setting aside of oneself and sometimes that means setting aside your personal opinion.
        </p>

    </div>
  <?php } elseif ($_SESSION["username"] === "andiebeca") {
   ?>
   <div class="daily-guide">
       <h2> The Week's Plan </h2>
       <h3> Let go. </h3>
       <p>
        It's not that Aquarians hold grudges, exactly, but they do remember a lot of disagreements and hurt feelings. Learn to let go!
       </p>
       <p class="last">
         Notice when you are frustrated and remind yourself that it is just a feeling. Holding a grude can do damage to your presence of mind in the long run.

       </p>
       <?php }
       ?>

    <!-- second random picture -->
    <div style="margin-top: 60px; margin-bottom: 10px;">
          <div class="row">
            <div class="col-2">
              <?php
              if (isset($_SESSION["username"])) {
                echo "<img src='img/stock/" . $b .  ".png'style='width: 9.5vw; height: 9.5vw; object-fit: cover; border-radius: 50%; border: solid 4px #fff; margin-left: ".rand(31,44)."vw;'/>";
              }
               ?>
            </div>
          </div>
    </div>

  <!--daily guide-->
  <?php
  if ($_SESSION["username"] === "SirFawkes") {
  ?>
    <div class="daily-guide">
        <h2> Today's Guide </h2>
        <p> Your Mars is in libra. This means you are gifted by an ability to give without strings attached.
          It’s not a fleeting attention, it’s your long-held cogenality. Today, use your ability to do a nice geasture
          for a stranger.</p>
        <h4> Ideas: </h4>
        <p style="margin-top:0"> Pay for the person behind you in line.
          <br>Offer up your seat.
          <br>Volunteer.
    </div>
  <?php } elseif ($_SESSION["username"] === "andiebeca") {
   ?>
   <div class="daily-guide">
       <h2> Today's Guide </h2>
       <p> Your Uranus is in Aquarius. This means people in your generation are redefining nonconformity. You are full of brilliant ideas on how to make connections. For today, Don't let society pressure you to second-guess yourself.</p>
       <h4> Ideas: </h4>
       <p style="margin-top:0"> Notice when you had a great laugh with someone else and appreciate it.
         <br>If you feel like hugging someone, do it.
         <br> Ask that one person out.
   </div>
<?php }
?>

    <!-- third random picture -->
    <div style="margin-top: 60px; margin-bottom: 10px;">
          <div class="row">
            <div class="col-2">
              <?php
              if (isset($_SESSION["username"])) {
                echo "<img src='img/stock/" . $c .  ".png'style='width: 9.5vw; height: 9.5vw; object-fit: cover; border-radius: 50%; border: solid 4px #fff; margin-left: ".rand(16,29)."vw;'/>";
              }
               ?>
            </div>
          </div>
    </div>

  </main>


  <!-- featured crytals -->
  <div class="categories">
    <div class="daily-guide">
      <h2 class="title">Featured Crystal Shop</h2>
      <div class="row" style="justify-content: center; padding: 40px 40px;">
        <div class="col-3">
          <?php
          if (isset($_SESSION["username"])) {
            echo "<img src='img/" . $_SESSION["username"] .  "/crystal1.png'/>";
          }
          if ($_SESSION["username"] === "SirFawkes") {
            echo "<br>
            <h4>Yellow Topaz</h4>
            <p>for building joy. <br> $1".rand(4,9).".99</p>";
          } elseif ($_SESSION["username"] === "andiebeca") {
            echo "<br>
            <h4>Selenite</h4>
            <p>to help a meditator connect to a higher consciousness. <br> $1".rand(4,9).".99</p>";
          }
          ?>
        </div>

        <div class="col-3">
          <?php
          if (isset($_SESSION["username"])) {
            echo "<img src='img/" . $_SESSION["username"] .  "/crystal2.png'/>";
          }
          if ($_SESSION["username"] === "SirFawkes") {
            echo "<br>
            <h4>Lapis Lazuli</h4>
            <p>for communication and connection. <br> $1".rand(4,9).".99</p>";
          } elseif ($_SESSION["username"] === "andiebeca") {
            echo "<br>
            <h4>Sodalite</h4>
            <p>for order and calmness in mind. <br> $1".rand(4,9).".99</p>";
          }
          ?>
        </div>

        <div class="col-3">
          <?php
          if (isset($_SESSION["username"])) {
            echo "<img src='img/" . $_SESSION["username"] .  "/crystal3.png'/>";
          }
          if ($_SESSION["username"] === "SirFawkes") {
            echo "<br>
            <h4>Peridot</h4>
            <p>for celebrating friendship. <br> $1".rand(4,9).".99</p>";
          }elseif ($_SESSION["username"] === "andiebeca") {
            echo "<br>
            <h4>Tiger’s Eye</h4>
            <p>to find clarity in your intentions. <br> $1".rand(4,9).".99</p>";
          }
          ?>
        </div>
    </div>
    </div>

  </div>


</body>
</html>
