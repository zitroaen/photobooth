<?php
#Load Settings
include ('config/config.php');
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="<?php echo $baseStyle; ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo $customizationStyle; ?>" type="text/css">
    <title> <?php echo $title; ?> </title>

</head>

<body>
    <?php include 'plugins/logo.php' ?>

    <div class="content">

        <h1><?php echo $headline; ?></h1>
        <h2><?php echo $subHeadline; ?></h2>

        <div class="buttonwrapper">
            <?php include 'plugins/takePhoto.php' ?>
            <?php include 'plugins/gallery.php' ?>
        </div>


    </div>

    <div id="photoFrame">
    </div>

</body>

</html>