<?php
# No toucy!#############
$prevCwd = getcwd();
chdir($_SERVER["DOCUMENT_ROOT"]); #Change to the servers root directory so all relations work, regardless from where they were called


########Appearence ############
#Style
$baseStyle = 'styles/baseStyle.css';
$phpStyle = 'styles/phpStyle.php';

#Texts
$title = 'websiteTitlePhotobox';
$headline = 'Überschrift';
$subHeadline = 'Name & Name';
$fontFamily = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
#Buttons
$galleryButtonText = "Galerie";
$galleryButtonImage = "resources/img/galleryButtonImage.svg";

$photoButtonText = "Foto";
$photoButtonImage = "resources/img/photoButtonImage.svg";

$printButtonText = "Drucken";
$printButtonImage = "resources/img/printButtonImage.svg";

#Images
$logoPath = 'resources/img/Logo.svg';
$backgroundImage = 'resources/img/background1.jpg';

##########Photos
$framePath = 'resources/frames/frame.png';
$printWithFrame = 1;



#Keycodes
$startPhotoCountdown = 13; #Keycode for starting the Photo Countdown (13=Enter)

#############################################################
#Other stuff that probably noone will ever need to touch
#############################################################

#Take Photo
$countdownTime = 3; #Time in seconds for the countdown
$displayTime = 10; #How long the picture should be showed after taking it

#
date_default_timezone_set('Europe/Berlin'); # Timezone for the picture filename
$photoPath = 'resources/photos/original/';  #Path where photos should be stored. Must end in a "/"
$fileExtension = 'jpg';


if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') { #detect OS of Server for testing purposes
    #Using Windows
    $takePhotoCommand = '"C:\Program Files (x86)\digiCamControl\CameraControlCmd.exe" /capture /filename ' . realpath($photoPath) . '\%s'; #%s is the placeholder for the filename that the script will automatically genrate 
} else {
    #Not using windows 
    $takePhotoCommand = 'gphoto2 --capture-image-and-download --filename=%s'; #%s is the placeholder for the filename that the script will automatically genrate 
}



#gallery
$nanogallery2Path = 'resources/js/nanogallery2/jquery.nanogallery2.core.js'; # path to the nanogallery2 installation
$nanogallery2PhotoProviderPath = 'resources/js/nanogallery2/jquery.nanogallery2.data_nano_photos_provider2.js'; # path to the nanogallery2 Photo Provider module
$nanogallery2Css = 'resources/js/nanogallery2/css/nanogallery2.css'; #path to the nanogallery2 CSS file

$nanoPhotosProviderPath = 'resources/photos/nano_photos_provider2.php';# path to the photos provider
$jqueryPath = 'resources/js/jquery-3.7.1.min.js'; #Path to jquery


#printing
$printingPath = 'resources/photos/print/'; #Folder where prints are generated to and printed from. Must end in a "/"
$printTargetWidth = 6000; #width of the document to be printed
$printTargetHeight = 4000; #height of the document to be printed
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') { #detect OS of Server for testing purposes
    #Using Windows
    $printCommand = 'echo this command is in for testing mspaint /pt %s'; #%s is the placeholder for the filename
} else {
    #Not using windows 
    $printCommand = 'lp -o landscape -o fit-to-page %s'; #%s is the placeholder for the filename
    #
}


chdir($prevCwd);
?>