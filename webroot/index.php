<?php
#Load Settings
include('config/config.php');
?>


<html>

<head>
    <link rel="stylesheet" href="<?php echo $baseStyle; ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo $customizationStyle; ?>" type="text/css">
    <title> <?php echo $title; ?> </title>
</head>

<body>
<?php include 'logo.php'?>


<h1><?php echo $headline; ?></h1>
<h2><?php echo $subHeadline; ?></h2>



<a href="gallery.php">
  <div id="galleryButton" class="nav">
    <?php echo $galleryButtonText; ?>
  </div>
  <img src="<?php echo $galleryButtonImage; ?>" alt="">
</a>

<a href="takePhoto.php">
  <div id="photoButton" class="nav">
    <?php echo $photoButtonText; ?>
  </div>
  <img src="<?php echo $photoButtonImage; ?>" alt="">
</a>



</body>
</html>


