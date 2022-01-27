<?php
  session_start();
 ?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Crystal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/styles.css" type="text/css">
  <link rel="stylesheet" href="css/bootstrap-grid.css" type="text/css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
  <script type='text/javascript' src='config.js'></script>
  <script type="text/javascript">
    let mykey = config.MY_GOOGLE_KEY;
  </script>
  <script async defer src= `https://maps.googleapis.com/maps/api/js?key=${mykey}libraries=places&callback=activatePlacesSearch`></script>
  <script src="js/script.js"></script>
  <script type='text/javascript' src='config.js'></script>
  <!--<link rel="stylesheet" href="css/stars.css" type="text/css" /> -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bellefair&family=Prata&family=Red+Hat+Display:wght@300&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Hind&family=Hind+Siliguri:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
