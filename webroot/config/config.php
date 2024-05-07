<?php
# No toucy!#############
$prevCwd = getcwd();
chdir($_SERVER["DOCUMENT_ROOT"]); #Change to the servers root directory so all relations work, regardless from where they were called



#Style
$baseStyle = 'styles/baseStyle.css';
$phpStyle = 'styles/phpStyle.php';



#Texts
$title = 'websiteTitlePhotobox';
$headline = 'Überschrift';
$subHeadline = 'Name & Name';
$fontFamily = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";


#Images
$logoPath = 'resources/img/Logo.svg';
$backgroundImage = 'resources/img/background.jpg';

#Buttons
$galleryButtonText = "Galerie";
$galleryButtonImage = "resources/img/galleryButtonImage.svg";

$photoButtonText = "Foto";
$photoButtonImage = "resources/img/photoButtonImage.svg";


#Keycodes
$startPhotoCountdown = 13; #Keycode for starting the Photo Countdown (13=Enter)

#############################################################
#Other stuff that probably noone will ever need to touch
#############################################################

#Take Photo
$countdownTime = 3; #Time in seconds for the countdown
$displayTime = 5; #How long the picture should be showed after taking it

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') { #detect OS of Server for testing purposes
    #Using Windows
    $takePhotoCommand = 'echo %s'; #%s is the placeholder for the filename that the script will automatically genrate 
} else {
    #Not using windows 
    $takePhotoCommand = 'gphoto2 --capture-image-and-download --filename=%s'; #%s is the placeholder for the filename that the script will automatically genrate 
}

#
date_default_timezone_set('Europe/Berlin'); # Timezone for the picture filename
$photoPath = 'resources/photos/original/';  #Path where photos should be stored. Must end in a "/"
$fileExtension = 'jpg';

#gallery
$nanogallery2Path = 'resources/js/nanogallery2/jquery.nanogallery2.core.js'; # path to the nanogallery2 installation
$nanogallery2PhotoProviderPath = 'resources/js/nanogallery2/jquery.nanogallery2.data_nano_photos_provider2.js'; # path to the nanogallery2 Photo Provider module
$nanogallery2Css = 'resources/js/nanogallery2/css/nanogallery2.css'; #path to the nanogallery2 CSS file

$nanoPhotosProviderPath = 'resources/photos/nano_photos_provider2.php';# path to the photos provider
$jqueryPath = 'resources/js/jquery-3.7.1.min.js'; #Path to jquery



chdir($prevCwd);
?>