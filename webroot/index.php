<?php
#Load Settings
include ('config/config.php');
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="<?php echo $baseStyle; ?>" type="text/css">

    <?php include ($phpStyle); ?>
    <title> <?php echo $title; ?> </title>
    <meta name="viewport" content="width=device-width, user-scalable=no">
</head>

<body>
    <?php include 'plugins/logo.php' ?>

    <div class="content">

        <h1><?php echo $headline; ?></h1>
        <h2><?php echo $subHeadline; ?></h2>

        <div class="buttonWrapper">
            <?php include 'plugins/photo.php' ?>
            <?php include 'plugins/gallery.php' ?> 
        </div>


    </div>

    <div id="photoFrame" class="modal">
        <div class="modal-frame">
            <span class="close">&times;</span>
            <div class="modal-content">

            </div>
        </div>
    </div>



    <div id="galleryFrame" class="modal">
        <div class="modal-frame">
            <span class="close">&times;</span>
            <div id="my-gallery" class="modal-content">

            </div>
        </div>
    </div>



</body>

</html>