<?php 

#Style
$baseStyle = 'styles/baseStyle.css';
$customizationStyle = 'styles/defaultCustomStyle.css';

#Texts
$title = 'websiteTitlePhotobox';
$headline = 'headline';
$subHeadline = 'subHeadline';

#Logo
$logoPath = 'resources/img/Logo.png';

#Buttons
$galleryButtonText = "gallery";
$galleryButtonImage = "resources/img/galleryButtonImage.svg";

$photoButtonText = "Käse!";
$photoButtonImage = "resources/img/photoButtonImage.svg";

#############################################################
#Other stuff that probably noone will ever need to touch
#############################################################

#Take Photo
$countDownTime = 3; #Time in seconds for the countdown
$takePhotoCommand = 'gphoto2 --capture-image-and-download --filename=%s'; #%s is the placeholder for the filename that the script will automatically genrate 
#$takePhotoCommand = 'echo %s'; #%s is the placeholder for the filename that the script will automatically genrate 
date_default_timezone_set('Europe/Berlin'); // Timezone for the picture filename
$photoPath = realpath('resources/photos/original');

#gallery
$nanogallery2Path = 'resources/js/nanogallery2/jquery.nanogallery2.core.js'; # path to the nanogallery2 installation
$nanoPhotosProviderPath = 'resources/photos/nano_photos_provider2.php';# path to the photos provider
$jqueryPath = 'resources/js/jquery-3.7.1.min.js'; #Path to jquery




?>