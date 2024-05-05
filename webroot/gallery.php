<?php
#Load Settings
include('config/config.php');
?>


<html>

<head>
    <link rel="stylesheet" href="<?php echo $baseStyle; ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo $customizationStyle; ?>" type="text/css">
    <title> <?php echo $title; ?> </title>

    <script  type="text/javascript"  src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <!--
    <script src="<?php echo $jqueryPath; ?>"></script>
-->
    <script src="<?php echo $nanogallery2Path; ?>"></script>
  </head>

<body>

<?php include 'logo.php'?>


<div id="nanogallery2">gallery_made_with_nanogallery2</div>


<script>

jQuery(document).ready(function () {
              jQuery("#nanogallery2").nanogallery2({
                thumbnailWidth:   'auto',
                thumbnailHeight:  150,
                kind:             'nano_photos_provider2',
                dataProvider:     '<?php echo $nanoPhotosProviderPath; ?>',
                locationHash:     false
              });
            });

</script>

            
</div>

</body>
</html>


